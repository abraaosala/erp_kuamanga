<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Repositories\Contracts\PayrollRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PayrollRepository implements PayrollRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Builder<PayrollRun> $query */
        $query = PayrollRun::query()->withCount('payslips');

        /** @var Collection<int, PayrollRun> $runs */
        $runs = $query->where('empresa_id', $this->empresaId())
            ->orderBy('period_start', 'desc')
            ->get();

        return $runs;
    }

    public function findById(int $id): ?PayrollRun
    {
        /** @var \Illuminate\Database\Eloquent\Builder<PayrollRun> $query */
        $query = PayrollRun::query()->withCount('payslips');

        /** @var \App\Models\PayrollRun|null $run */
        $run = $query->where('empresa_id', $this->empresaId())
            ->find($id);

        return $run;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): PayrollRun
    {
        $data['empresa_id'] ??= $this->empresaId();
        return PayrollRun::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $run = $this->findById($id);
        if (!$run) {
            return false;
        }
        return $run->update($data);
    }

    public function delete(int $id): bool
    {
        $run = $this->findById($id);
        if (!$run) {
            return false;
        }
        return (bool) $run->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        /** @var \Illuminate\Database\Eloquent\Builder<PayrollRun> $query */
        $query = PayrollRun::query()->withCount('payslips');

        $q = $query->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where('description', 'like', "%{$search}%");
        }

        return $q->orderBy('period_start', 'desc')->paginate($perPage);
    }

    public function findPayslipsByRun(int $payrollRunId): Collection
    {
        /** @var Collection<int, Payslip> $payslips */
        $payslips = Payslip::with('employee.position')
            ->where('empresa_id', $this->empresaId())
            ->where('payroll_run_id', $payrollRunId)
            ->get();

        return $payslips;
    }

    public function findPayslipById(int $payslipId): ?Payslip
    {
        /** @var \App\Models\Payslip|null $payslip */
        $payslip = Payslip::with('employee.position', 'employee.department', 'payrollRun')
            ->where('empresa_id', $this->empresaId())
            ->find($payslipId);

        return $payslip;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createPayslip(array $data): Payslip
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Payslip::create($data);
    }

    /**
     * @param array<int, array<string, mixed>> $payslipsData
     */
    public function createPayslips(array $payslipsData): void
    {
        foreach ($payslipsData as $data) {
            $this->createPayslip($data);
        }
    }

    public function deletePayslipsByRun(int $payrollRunId): bool
    {
        /** @var int $deleted */
        $deleted = Payslip::where('empresa_id', $this->empresaId())
            ->where('payroll_run_id', $payrollRunId)
            ->delete();

        return $deleted > 0;
    }
}
