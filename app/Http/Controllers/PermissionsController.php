<?php

namespace App\Http\Controllers;

use App\Services\PermissionsCrudService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    protected PermissionsCrudService $crudService;

    public function __construct(PermissionsCrudService $permissionsService)
    {
        $this->crudService = $permissionsService;
    }

    public function create(Request $request): JsonResponse
    {
        return $this->crudService->create($request, new Permission());
    }

    public function update(Request $request): JsonResponse
    {
        return $this->crudService->update($request, new Permission());
    }

    public function delete(Request $request): JsonResponse
    {
        return $this->crudService->delete($request, new Permission());
    }
}
