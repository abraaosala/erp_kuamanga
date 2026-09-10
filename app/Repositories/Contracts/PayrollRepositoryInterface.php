<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\PayrollRun;
use App\Models\Payslip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PayrollRepositoryInterface
{
    /**
     * @return Collection<int, PayrollRun>
     */
    public function all(): Collection;

    public function findById(int $id): ?PayrollRun;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): PayrollRun;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, PayrollRun>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @return Collection<int, Payslip>
     */
    public function findPayslipsByRun(int $payrollRunId): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function createPayslip(array $data): Payslip;

    /**
     * @param array<int, array<string, mixed>> $payslipsData
     */
    public function createPayslips(array $payslipsData): void;

    public function deletePayslipsByRun(int $payrollRunId): bool;
}