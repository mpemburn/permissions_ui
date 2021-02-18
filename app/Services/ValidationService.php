<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ValidationService
{
    protected Collection $errors;

    public function __construct()
    {
        $this->errors = collect();
    }

    public function handle(Request $request, array $rules): bool
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->addError($validator->errors()->first());

            return false;
        }

        return true;
    }

    public function addError(string $error): void
    {
        $this->errors->push($error);
    }

    public function getMessage(): string
    {
        return $this->errors->first();
    }

    public function getAllErrors(): Collection
    {
        return $this->errors;
    }

    public function hasError(): bool
    {
        return $this->errors->isNotEmpty();
    }

}
