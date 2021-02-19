<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionsCrudService
{
    protected ValidationService $validator;

    public function __construct(ValidationService $validationService)
    {
        $this->validator = $validationService;
    }

    public function create(Request $request, Model $model): JsonResponse
    {
        $modelId = null;
        $name = null;

        if ($this->validator->handle($request, [
            'name' => ['required', 'unique:' . $model->getTable(), 'max:255']
        ])) {
            $name = $request->get('name');
            try {
                $model->name = $name;
                $model->guard_name = 'web';
                $model->save();
                $modelId = $model->id;
            } catch (\Exception $e) {
                $this->validator->addError($e->getMessage());
                Log::debug($e->getMessage());
            }
        }

        if ($this->validator->hasError()) {
            return response()->json(['error' => $this->validator->getMessage()], 400);
        }

        return response()->json([
            'success' => true,
            'id' => $modelId,
            'name' => $name
        ]);
    }

    public function update(Request $request, Model $model): JsonResponse
    {
        if ($this->validator->handle($request, [
            'name' => ['required', 'max:255']
        ])) {
            $model = $this->find($request, $model);
            if (! $model) {
                return response()->json(['error' => $this->validator->getMessage()], 400);
            }
            try {
                $model->update([
                    'name' => $request->get('name'),
                    'guard_name' => 'web'
                ]);
                $model->save();
            } catch (\Exception $e) {
                $this->validator->addError($e->getMessage());
                Log::debug($e->getMessage());
            }
        }

        if ($this->validator->hasError()) {
            return response()->json(['error' => $this->validator->getMessage()], 400);
        }

        return response()->json([
            'success' => true,
            'id' => $model->id
        ]);
    }

    public function delete(Request $request, Model $model): JsonResponse
    {
        $model = $this->find($request, $model);
        if (!$model) {
            return response()->json(['error' => $this->validator->getMessage()], 400);
        }
        try {
            $model->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true]);
    }

    protected function find(Request $request, Model $model): ?Model
    {
        $modelId = $request->get('id');

        Log::debug($modelId);
        try {
            $model = $model->findById($modelId, 'web');
        } catch (\Exception $e) {
            $this->validator->addError($e->getMessage());
            Log::debug($e->getMessage());

            return null;
        }

        return $model;
    }
}
