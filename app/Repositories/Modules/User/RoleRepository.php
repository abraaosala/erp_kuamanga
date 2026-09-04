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
        /** @var Collection<int, Role> $result */
        $result = Role::all();

        return $result;
    }

    public function findById(int $id): ?Role
    {
        /** @var \App\Models\Role|null $role */
        $role = Role::with('permissions')->find($id);

        return $role;
    }

    public function findByName(string $name): ?Role
    {
        /** @var \App\Models\Role|null $role */
        $role = Role::where('name', $name)->first();

        return $role;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function assignToUser(int $roleId, int $userId): void
    {
        /** @var Role $role */
        $role = Role::findOrFail($roleId);
        $role->users()->syncWithoutDetaching([$userId]);
    }

    public function removeFromUser(int $roleId, int $userId): void
    {
        /** @var Role $role */
        $role = Role::findOrFail($roleId);
        $role->users()->detach($userId);
    }
}
