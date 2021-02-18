<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

            if ($request->has('role_permission')) {
                $role = Role::find($modelId);
                $this->processPermissions($role, $request);
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

            if ($request->has('role_permission')) {
                $role = Role::find($model->id);
                $this->processPermissions($role, $request);
            }
        }

        if ($this->validator->hasError()) {
            return response()->json(['error' => $this->validator->getMessage()], 400);
        }

        return response()->json(['success' => true]);
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

    protected function processPermissions(Role $role, Request $request): void
    {
        $currentUserPermissions = $this->getCurrentRolePermissions($role);
        $permissionsFromEditor = $this->getPermissionsFromEditorCheckboxes($request);

        $this->addPermissions($role, $permissionsFromEditor, $currentUserPermissions);
        if ($currentUserPermissions->isNotEmpty()) {
            $this->removePermissions($role, $permissionsFromEditor, $currentUserPermissions);
        }
    }

    protected function getCurrentRolePermissions(Role $role): Collection
    {
        return $role->getAllPermissions()->map(static function (Permission $item) {
            return $item->name;
        });
    }

    protected function getPermissionsFromEditorCheckboxes(Request $request): Collection
    {
        return collect($request->get('role_permission'));
    }

    protected function addPermissions(Role $role, $permissionsFromEditor, $currentUserPermissions): void
    {
        $toBeAdded = $permissionsFromEditor->diff($currentUserPermissions);
        if ($toBeAdded->isNotEmpty()) {
            $toBeAdded->values()->each(function (string $permission) use ($role) {
                try {
                    $role->givePermissionTo($permission);
                } catch (PermissionDoesNotExist $e) {
                    $this->validator->addError($e->getMessage());
                    Log::debug($e->getMessage());
                }
            });
        }
    }

    protected function removePermissions(Role $role, $permissionsFromEditor, $currentUserPermissions): void
    {
        $toBeRemoved = $currentUserPermissions->diff($permissionsFromEditor);
        if ($toBeRemoved->isNotEmpty()) {
            $toBeRemoved->values()->each(function (string $permission) use ($role) {
                try {
                    $role->revokePermissionTo($permission);
                } catch (PermissionDoesNotExist $e) {
                    $this->validator->addError($e->getMessage());
                    Log::debug($e->getMessage());
                }
            });
        }
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
