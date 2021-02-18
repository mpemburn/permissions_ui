<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportIssue;
use App\Models\ReportLine;
use App\Objects\IssueCollection;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ReportService
{
    public const MAC_ADDRESS_PATTERN = '/[\w]{2}:[\w]{2}:[\w]{2}:[\w]{2}:[\w]{2}:[\w]{2}/';

    public function getFileList(): array
    {
        $path = 'public/data/';
        $dir = Storage::disk('local')->files($path);

        return collect($dir)->map(static function ($file) use ($path) {
            return str_replace($path, '', $file);
        })->sort()->toArray();
    }

    public function getReportList(): array
    {
        return Report::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getReportByUid(string $uid): IssueCollection
    {
        $issueCollection = new IssueCollection();

        $report = Report::query()
            ->where('uid', '=', $uid)
            ->first();
        if ($report) {
            // Add the report header
            $issueCollection->addIssue(new ReportIssue(ReportIssue::HEADER_ARRAY));
            $issueCollection->setFilename($report->file_name);
            // Add the issues from database
            ReportIssue::query()->where('report_id', '=', $report->id)
                ->each(static function (ReportIssue $reportIssue) use (&$issueCollection) {
                    $issueCollection->addIssue($reportIssue);
                });
        }

        return $issueCollection;
    }

    public function getWyebotIssues(string $csvFile): IssueCollection
    {
        $issueCollection = new IssueCollection();

        $reader = new Csv();
        $reader->setInputEncoding('CP1252');
        $reader->setDelimiter(',');
        $reader->setEnclosure('');
        $reader->setSheetIndex(0);

        if ($reader) {
            $csv = $reader->load(storage_path('app/public/data/') . $csvFile);
            $data = $csv->getActiveSheet()->toArray();
            $issueCollection->loadIssuesFromCsvData($data);
        }

        return $issueCollection;
    }

    public function receiveUploadedReports($uploadArray): void
    {
        collect($uploadArray)->each(function ($file) {
            $uploadFileName = $file->getClientOriginalName();

            // Upload file to public path in storage directory
            $file->move(storage_path('app/public/data'), $uploadFileName);
            $ext = $file->getClientOriginalExtension();
            $this->storeReport($uploadFileName, $ext);
        });
    }

    public function storeReport(string $filename, $extension = 'csv'): void
    {
        $issueCollection = $this->getWyebotIssues($filename);
        $issues = $issueCollection->getIssues();

        $report = new Report([
            'uid' => uniqid('', true),
            'file_name' => str_replace('.' . $extension, '', $filename),
        ]);
        $report->save();

        $issues->each(static function (ReportIssue $issue) use ($report, $issueCollection) {
            if ($issue->uid) {
                $reportIssue = new ReportIssue([
                    'report_id' => $report->id,
                    'severity' => $issue->severity,
                    'problem' => $issue->problem,
                    'solution' => $issue->solution,
                    'uid' => $issue->uid,
                ]);
                $reportIssue->save();

                if ($issueCollection->hasAffectedDevices($issue->uid)) {
                    $issueCollection->getAffectedDevices($issue->uid)
                        ->each(static function ($line) use ($report, $reportIssue) {
                            $lineData = is_array($line) ? current($line) : null;
                            if ($lineData) {
                                preg_match_all(self::MAC_ADDRESS_PATTERN, $lineData, $matches);
                                $macAddresses = collect($matches)
                                    ->flatten()
                                    ->unique()
                                    ->implode(',');

                                $reportLine = new ReportLine([
                                    'report_id' => $report->id,
                                    'data' => $lineData,
                                    'mac_addresses' => $macAddresses,
                                ]);
                                $reportLine->report_issue_id = $reportIssue->id;

                                $reportLine->save();
                            }
                        });
                }
            }
        });
    }
}
