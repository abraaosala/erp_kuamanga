<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Benefit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BenefitServiceInterface
{
    /**
     * @return Collection<int, Benefit>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?Benefit;

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
    public function eligibleEmployees(int $benefitId): Collection;

    public function assign(int $benefitId, int $employeeId, ?string $startedAt = null, ?string $notes = null): bool;

    public function unassign(int $benefitId, int $employeeId): bool;

    public function isEligible(int $benefitId, int $employeeId): bool;

    /**
     * @return array{total: int, active: int, assignments: int}
     */
    public function summary(): array;

    /**
     * @return array<int, int>
     */
    public function assignedCounts(): array;
}