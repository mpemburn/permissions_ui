<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesService
{
    public const NO_ROLE_NAME_PROVIDED = 'No role name provided.';

    public function getPermissionsForRole(Request $request): JsonResponse
    {
        $permissions = [];
        $roleName = $request->get('role_name');
        if ($roleName) {
            $role = Role::findByName($roleName, 'web');
            $role->getAllPermissions()->each(static function (Permission $permission) use (&$permissions) {
                $permissions[] = $permission->name;
            });
        } else {
            return response()->json(['error' => self::NO_ROLE_NAME_PROVIDED], 400);
        }

        return response()->json(['success' => true, 'permissions' => $permissions]);
    }
}
