<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Models\User;
use App\DTO\Permissions\CreatePermissionDTO;
use App\DTO\Permissions\EditPermissionDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionRepository
{
    public function __construct(protected Permission $permission)
    {

    }

    public function getPaginate(int $totalPerPage = 15, int $page = 1, string $filter = ''): LengthAwarePaginator
    {
        return $this->permission->where(function ($query) use ($filter) {
            if ($filter !== '') {
                $query->where('name', 'like', '%{$filter}%');
            }
        })->paginate($totalPerPage, ['*'], 'page', $page);
    }

    public function createNew(CreatePermissionDTO $dto): Permission
    {
        return $this->permission->create((array) $dto);
    }

    public function findById(string $id): ?Permission
    {
        return $this->permission->find($id);
    }

    public function update(EditPermissionDTO $dto): bool
    {
        if (!$permission = $this->findById($dto->id)) {
            return false;
        }

        return $permission->update((array) $dto);
    }

    public function delete(string $id): bool
    {
        if (!$permission = $this->findById($id)) {
            return false;
        }

        return $permission->delete();
    }
}
