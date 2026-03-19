<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function findById(int $id): ?\App\Models\User;

    public function findByEmail(string $email): ?\App\Models\User;

    public function create(array $data): \App\Models\User;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
