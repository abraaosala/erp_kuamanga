<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role>
     */
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function findById(int $id): ?\App\Models\Role;

    public function findByName(string $name): ?\App\Models\Role;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): \App\Models\Role;

    public function assignToUser(int $roleId, int $userId): void;

    public function removeFromUser(int $roleId, int $userId): void;
}
