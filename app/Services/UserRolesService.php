<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolesService
{
    public const USER_NOT_FOUND_ERROR = 'This user was not found in the system';
    public const GET_ASSIGNED_PERMISSIONS_ENDPOINT = '/api/user_roles/assigned';

    protected ?string $errorMessage = null;
    protected ValidationService $validator;

    public function __construct(ValidationService $validationService)
    {
        $this->validator = $validationService;
    }

    protected function hasError(): bool
    {
        return ! empty($this->errorMessage);
    }

    public function isCurrentUserAdmin(): bool
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);

        return $user->hasRole('Administrator') ? true : false;
    }

    public function edit(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        $user = User::find($userId);

        if ($user) {
            $this->processRoles($user, $request);
            $this->processPermissions($user, $request);
        } else {
            $this->errorMessage = self::USER_NOT_FOUND_ERROR;
        }

        if ($this->hasError()) {
            return response()->json(['error' => $this->errorMessage], 400);
        }

        return response()->json(['success' => true]);
    }

    public function getPermissionsAssignedToRole(Request $request): JsonResponse
    {
        $permissions = [];

        if ($this->validator->handle($request, [
            'role_name' => ['required']
        ])) {
            $roleName = $request->get('role_name');
            $role = Role::findByName($roleName, 'web');

            $permissions = $role->getAllPermissions()->map(static function (Permission $permission) {
                return $permission->name;
            })->toArray();
        }

        if ($this->validator->hasError()) {
            return response()->json(['error' => $this->validator->getMessage()], 400);
        }

        return response()->json(['success' => true, 'permissions' => $permissions]);
    }

    protected function processRoles(User $user, Request $request): void
    {
        $currentUserRoles = $this->getCurrentUserRoles($user);
        $rolesFromEditor = $this->getValuesFromEditorCheckboxes($request, 'role');

        $this->addRoles($user, $currentUserRoles, $rolesFromEditor);
        if ($currentUserRoles->isNotEmpty()) {
            $this->removeRoles($user, $currentUserRoles, $rolesFromEditor);
        }
    }

    protected function processPermissions(User $user, Request $request): void
    {
        $currentUserPermissions = $this->getCurrentUserPermissions($user);
        $permissionsFromEditor = $this->getValuesFromEditorCheckboxes($request, 'permission');

        $this->addPermissions($user, $permissionsFromEditor, $currentUserPermissions);
        if ($currentUserPermissions->isNotEmpty()) {
            $this->removePermissions($user, $permissionsFromEditor, $currentUserPermissions);
        }
    }

    protected function addRoles(User $user, $currentUserRoles, $rolesFromEditor): void
    {
        $toBeAdded = $rolesFromEditor->diff($currentUserRoles);
        if ($toBeAdded->isNotEmpty()) {
            $toBeAdded->values()->each(function (string $role) use ($user) {
                try {
                    $user->assignRole($role);
                } catch (RoleDoesNotExist $e) {
                    $this->errorMessage = $e->getMessage();
                }
            });
        }
    }

    protected function removeRoles(User $user, $currentUserRoles, $rolesFromEditor): void
    {
        $toBeRemoved = $currentUserRoles->diff($rolesFromEditor);
        if ($toBeRemoved->isNotEmpty()) {
            $toBeRemoved->values()->each(function (string $role) use ($user) {
                try {
                    $user->removeRole($role);
                } catch (RoleDoesNotExist $e) {
                    $this->errorMessage = $e->getMessage();
                }
            });
        }
    }

    protected function addPermissions(User $user, $permissionsFromEditor, $currentUserPermissions): void
    {
        $toBeAdded = $permissionsFromEditor->diff($currentUserPermissions);
        if ($toBeAdded->isNotEmpty()) {
            $toBeAdded->values()->each(function (string $permission) use ($user) {
                try {
                    $user->givePermissionTo($permission);
                } catch (PermissionDoesNotExist $e) {
                    $this->errorMessage = $e->getMessage();
                }
            });
        }
    }

    protected function removePermissions(User $user, $permissionsFromEditor, $currentUserPermissions): void
    {
        $toBeRemoved = $currentUserPermissions->diff($permissionsFromEditor);
        if ($toBeRemoved->isNotEmpty()) {
            $toBeRemoved->values()->each(function (string $permission) use ($user) {
                try {
                    $user->revokePermissionTo($permission);
                } catch (PermissionDoesNotExist $e) {
                    $this->errorMessage = $e->getMessage();
                }
            });
        }
    }

    protected function getCurrentUserRoles(User $user): Collection
    {
        return $user->roles()->pluck('name');
    }

    protected function getValuesFromEditorCheckboxes(Request $request, string $entityType): Collection
    {
        return collect($request->get($entityType));
    }

    protected function getCurrentUserPermissions(User $user): Collection
    {
        return $user->getAllPermissions()->map(static function (Permission $item) {
            return $item->name;
        });
    }
}
