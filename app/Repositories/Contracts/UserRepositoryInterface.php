<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\User>
     */
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function findById(int $id): ?\App\Models\User;

    public function findByEmail(string $email): ?\App\Models\User;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): \App\Models\User;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\User>
     */
    public function paginate(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
