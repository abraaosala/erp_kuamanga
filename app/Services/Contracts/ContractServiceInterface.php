<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContractServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?Contract;

    public function create(array $data): Contract;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function getByEmployee(int $employeeId): Collection;
}
