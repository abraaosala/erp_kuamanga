<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface UserServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\User>
     */
    public function getAllUsers(): \Illuminate\Database\Eloquent\Collection;

    public function getUserById(int $id): ?\App\Models\User;

    /**
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): \App\Models\User;

    /**
     * @param array<string, mixed> $data
     */
    public function updateUser(int $id, array $data): bool;

    public function deleteUser(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\User>
     */
    public function paginateUsers(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
