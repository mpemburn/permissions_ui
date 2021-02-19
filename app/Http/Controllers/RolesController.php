<?php

namespace App\Http\Controllers;

use App\Services\PermissionsAssociationService;
use App\Services\PermissionsCrudService;
use App\Services\RolesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    protected PermissionsCrudService $crudService;
    protected PermissionsAssociationService $permissionsAssociationService;
    protected RolesService $rolesService;

    public function __construct(
        PermissionsCrudService $crudService,
        PermissionsAssociationService $permissionsAssociationService,
        RolesService $rolesService
    )
    {
        $this->crudService = $crudService;
        $this->rolesService = $rolesService;
        $this->permissionsAssociationService = $permissionsAssociationService;
    }

    public function create(Request $request): JsonResponse
    {
        $response = $this->crudService->create($request, new Role());

        if ($request->has('role_permission')) {
            return $this->processPermissions($request, $response);
        }

        return $response;
    }

    public function update(Request $request): JsonResponse
    {
        $response = $this->crudService->update($request, new Role());

        if ($request->has('role_permission')) {
            return $this->processPermissions($request, $response);
        }

        return $response;
    }

    public function delete(Request $request): JsonResponse
    {
        return $this->crudService->delete($request, new Role());
    }

    public function getPermissions(Request $request): JsonResponse
    {
        return $this->rolesService->getPermissionsForRole($request);
    }

    protected function processPermissions(Request $request, JsonResponse $response): JsonResponse
    {
        if ($response->getStatusCode() !== 200) {
            return $response;
        }
        // The ID of new or update Role is passed back in the response
        $roleId = $response->getData()->id;
        $role = Role::find($roleId);

        return $this->permissionsAssociationService->process($role, $request);
    }
}
