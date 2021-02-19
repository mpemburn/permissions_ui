<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsAssociationService
{
    protected ValidationService $validator;

    public function __construct(ValidationService $validationService)
    {
        $this->validator = $validationService;
    }

    public function process(Role $role, Request $request): JsonResponse
    {
        $currentUserPermissions = $this->getCurrentRolePermissions($role);
        $permissionsFromEditor = $this->getPermissionsFromEditorCheckboxes($request);

        $this->addPermissions($role, $permissionsFromEditor, $currentUserPermissions);
        if ($currentUserPermissions->isNotEmpty()) {
            $this->removePermissions($role, $permissionsFromEditor, $currentUserPermissions);
        }

        if ($this->validator->hasError()) {
            return response()->json(['error' => $this->validator->getMessage()], 400);
        }

        return response()->json(['success' => true]);
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
}
