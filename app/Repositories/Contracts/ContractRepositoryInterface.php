<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContractRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Contract;

    public function create(array $data): Contract;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function findByEmployee(int $employeeId): Collection;
}
