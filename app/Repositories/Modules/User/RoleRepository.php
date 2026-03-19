<?php

declare(strict_types=1);

namespace App\Repositories\Modules\User;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function all(): Collection
    {
        return Role::all();
    }

    public function findById(int $id): ?Role
    {
        return Role::with('permissions')->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function assignToUser(int $roleId, int $userId): void
    {
        $role = Role::findOrFail($roleId);
        $role->users()->syncWithoutDetaching([$userId]);
    }

    public function removeFromUser(int $roleId, int $userId): void
    {
        $role = Role::findOrFail($roleId);
        $role->users()->detach($userId);
    }
}
