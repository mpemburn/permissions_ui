<?php

namespace App\Http\Controllers;

use App\Services\PermissionsCrudService;
use App\Services\RolesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    protected PermissionsCrudService $crudService;
    protected RolesService $rolesService;

    public function __construct(PermissionsCrudService $crudService, RolesService $rolesService)
    {
        $this->crudService = $crudService;
        $this->rolesService = $rolesService;
    }

    public function create(Request $request): JsonResponse
    {
        return $this->crudService->create($request, new Role());
    }

    public function update(Request $request): JsonResponse
    {
        return $this->crudService->update($request, new Role());
    }

    public function delete(Request $request): JsonResponse
    {
        return $this->crudService->delete($request, new Role());
    }

    public function getPermissions(Request $request): JsonResponse
    {
        return $this->rolesService->getPermissionsForRole($request);
    }
}
