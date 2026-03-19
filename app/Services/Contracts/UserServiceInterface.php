<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface UserServiceInterface
{
    public function getAllUsers(): \Illuminate\Database\Eloquent\Collection;

    public function getUserById(int $id): ?\App\Models\User;

    public function createUser(array $data): \App\Models\User;

    public function updateUser(int $id, array $data): bool;

    public function deleteUser(int $id): bool;

    public function paginateUsers(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
