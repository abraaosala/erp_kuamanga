<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContractRepositoryInterface
{
    /**
     * @return Collection<int, Contract>
     */
    public function all(): Collection;

    public function findById(int $id): ?Contract;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Contract;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, Contract>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @return Collection<int, Contract>
     */
    public function findByEmployee(int $employeeId): Collection;
}
