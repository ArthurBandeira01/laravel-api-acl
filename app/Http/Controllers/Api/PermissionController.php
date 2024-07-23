<?php

namespace App\Http\Controllers\Api;

use App\DTO\Permissions\CreatePermissionDTO;
use App\DTO\Permissions\EditPermissionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePermissionRequest;
use App\Http\Requests\Api\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(private PermissionRepository $permissionRepository)
    {

    }

    public function index(Request $request)
    {
        $permissions = $this->permissionRepository->getPaginate(
            totalPerPage: $request->total_per_page ?? 15,
            page: $request->page ?? 1,
            filter: $request->get('filter', ''),
        );

        return PermissionResource::collection($permissions);
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = $this->permissionRepository->createNew(
            new CreatePermissionDTO(... $request->validated())
        );

        return new PermissionResource($permission);
    }

    public function show(string $id)
    {
        if (!$permission = $this->permissionRepository->findById($id)) {
            return response()->json(['message' => 'Permission not found'], Response::HTTP_NOT_FOUND);
        }

        return new PermissionResource($permission);
    }

    public function update(UpdatePermissionRequest $request, string $id)
    {
        $response = $this->permissionRepository->update(new EditPermissionDTO(...[$id, ...$request->validated()]));
        if (!$response) {
            return response()->json(['message' => 'Permission not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Permission updated with success!']);
    }


    public function destroy(string $id)
    {
        if (!$this->permissionRepository->delete($id)) {
            return response()->json(['message' => 'Permission not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Permission deleted with success!'], Response::HTTP_NO_CONTENT);
    }
}
