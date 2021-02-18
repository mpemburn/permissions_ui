<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Services\UserRolesService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    protected AuthService $authService;
    protected UserRolesService $userRolesService;

    public function __construct(AuthService $authService, UserRolesService $userRolesService)
    {
        $this->authService = $authService;
        $this->userRolesService = $userRolesService;
    }

    public function roles()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('roles.index')
            ->with('action', '/api/roles/')
            ->with('roles', $roles)
            ->with('permissions', $permissions)
            ->with('token', $this->authService->getAuthToken());
    }

    public function permissions()
    {
        $permissions = Permission::all();

        return view('permissions.index')
            ->with('action', '/api/permissions/')
            ->with('permissions', $permissions)
            ->with('token', $this->authService->getAuthToken());
    }


    public function userRoles()
    {
        $users = User::all();

        return view('user-roles.index')
            ->with('action', '/api/user_roles/')
            ->with('users', $users)
            ->with('currentUserIsAdmin', $this->userRolesService->isCurrentUserAdmin())
            ->with('getAssignedEnpoint', UserRolesService::GET_ASSIGNED_PERMISSIONS_ENDPOINT)
            ->with('roles', Role::all())
            ->with('permissions', Permission::all())
            ->with('token', $this->authService->getAuthToken());
    }
}
