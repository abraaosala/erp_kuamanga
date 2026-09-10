<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\PayrollRun;
use App\Models\Payslip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PayrollServiceInterface
{
    /**
     * @return Collection<int, PayrollRun>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?PayrollRun;

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
    public function getPayslipsByRun(int $payrollRunId): Collection;

    /**
     * Gera a folha salarial do período: calcula salário base (contrato ativo),
     * horas extra (banco de horas) e descontos por faltas (assiduidade),
     * criando a run e os payslips.
     *
     * @return array{run: PayrollRun, payslips: Collection<int, Payslip>}
     */
    public function runFromContracts(string $periodStart, string $periodEnd, string $description): array;

    /**
     * Informa se existe ao menos um funcionário com contrato activo elegível
     * para processamento de folha salarial.
     */
    public function hasEligibleEmployees(): bool;
}