<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Benefit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BenefitRepositoryInterface
{
    /**
     * @return Collection<int, Benefit>
     */
    public function all(): Collection;

    public function findById(int $id): ?Benefit;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Benefit;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Benefit>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @return Collection<int, \App\Models\Employee>
     */
    public function employeesByBenefit(int $benefitId): Collection;

    /**
     * @return Collection<int, \App\Models\Employee>
     */
    public function employeesNotAssigned(int $benefitId): Collection;

    /**
     * @param array<string, mixed> $meta
     */
    public function assign(int $benefitId, int $employeeId, array $meta = []): bool;

    public function unassign(int $benefitId, int $employeeId): bool;

    public function existsAssignment(int $benefitId, int $employeeId): bool;

    /**
     * @return array<int, int>
     */
    public function assignedCounts(): array;
}