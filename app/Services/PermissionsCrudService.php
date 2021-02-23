<?php

namespace App\Services;

use App\Interfaces\UiInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsCrudService
{
    protected ValidationService $validator;

    public function __construct(ValidationService $validationService)
    {
        $this->validator = $validationService;
    }

    public function getAllRoles(): Collection
    {
        return Role::query()
            ->orderBy('name')
            ->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get();
    }

    public function getAllUsers(): Collection
    {
        return User::query()
            ->orderBy('name')
            ->get();
    }

    public function getProtectedRoles(): ?array
    {
        return explode(',', env('PROTECTED_ROLES'));
    }

    public function create(Request $request, UiInterface $model): JsonResponse
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

    public function update(Request $request, UiInterface $model): JsonResponse
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

    public function delete(Request $request, UiInterface $model): JsonResponse
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

    protected function find(Request $request, UiInterface $model): ?UiInterface
    {
        $class = get_class($model);
        $modelId = $request->get('id');
        if (! $modelId) {
            $this->validator->addError($class . ' not found by ID');
            return null;
        }

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
