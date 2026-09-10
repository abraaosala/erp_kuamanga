<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\HourBankEntryRepositoryInterface;
use App\Repositories\Contracts\PayrollRepositoryInterface;
use App\Services\Contracts\PayrollServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PayrollService implements PayrollServiceInterface
{
    private const MONTHLY_HOURS = 173.33;

    private const DAYS_IN_MONTH = 30;

    private const OVERTIME_RATE = 1.5;

    protected IrtCalculator $irtCalculator;

    public function __construct(
        protected PayrollRepositoryInterface $payrollRepository,
        protected EmployeeRepositoryInterface $employeeRepository,
        protected ContractRepositoryInterface $contractRepository,
        protected HourBankEntryRepositoryInterface $hourBankEntryRepository,
        protected AttendanceRepositoryInterface $attendanceRepository
    ) {
        $this->irtCalculator = new IrtCalculator();
    }

    public function getAll(): Collection
    {
        return $this->payrollRepository->all();
    }

    public function getById(int $id): ?PayrollRun
    {
        return $this->payrollRepository->findById($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): PayrollRun
    {
        return $this->payrollRepository->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        return $this->payrollRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $run = $this->getById($id);
        if (!$run) {
            return false;
        }
        $this->payrollRepository->deletePayslipsByRun($id);
        return $this->payrollRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->payrollRepository->paginate($perPage, $search);
    }

    public function getPayslipsByRun(int $payrollRunId): Collection
    {
        return $this->payrollRepository->findPayslipsByRun($payrollRunId);
    }

    public function getPayslipById(int $payslipId): ?Payslip
    {
        return $this->payrollRepository->findPayslipById($payslipId);
    }

    public function hasEligibleEmployees(): bool
    {
        return $this->contractRepository->hasActiveEligiblePayroll();
    }

    /**
     * @return array{run: PayrollRun, payslips: Collection<int, Payslip>}
     */
    public function runFromContracts(string $periodStart, string $periodEnd, string $description): array
    {
        if (!$this->contractRepository->hasActiveEligiblePayroll()) {
            throw new \RuntimeException(
                'Não é possível gerar a folha salarial: não existem funcionários com contrato activo elegível. '
                . 'Cadastre um contrato activo com salário base antes de processar.'
            );
        }

        /** @var Collection<int, Employee> $employees */
        $employees = $this->employeeRepository->all();

        $run = $this->payrollRepository->create([
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'description'  => $description,
            'status'       => 'rascunho',
        ]);

        $payslipsData = [];
        $totalGross = 0.0;
        $totalDeductions = 0.0;
        $totalNet = 0.0;

        foreach ($employees as $employee) {
            /** @var \App\Models\Employee $employee */
            /** @var Contract|null $contract */
            $contract = $this->contractRepository->findActiveByEmployee($employee->id);
            if (!$contract) {
                continue;
            }

            /** @var mixed $baseSalaryRaw */
            $baseSalaryRaw = $contract->salario_base;
            $baseSalary = is_numeric($baseSalaryRaw) ? (float) $baseSalaryRaw : 0.0;
            if ($baseSalary <= 0) {
                continue;
            }

            $overtimeHours = $this->hourBankEntryRepository->overtimeHoursBetween($employee->id, $periodStart, $periodEnd);
            $absentDays = $this->attendanceRepository->absentDaysBetween($employee->id, $periodStart, $periodEnd);

            $hourlyRate = $baseSalary / self::MONTHLY_HOURS;
            $overtimeAmount = $overtimeHours > 0 ? $overtimeHours * $hourlyRate * self::OVERTIME_RATE : 0.0;
            $dailyRate = $baseSalary / self::DAYS_IN_MONTH;
            $absentDeduction = $absentDays > 0 ? $absentDays * $dailyRate : 0.0;

            $gross = $baseSalary + $overtimeAmount;
            $socialSecurity = $this->irtCalculator->socialSecurity($gross);
            $taxableIncome = $gross - $socialSecurity;
            $irt = $this->irtCalculator->tax($taxableIncome);
            $net = $gross - $socialSecurity - $irt - $absentDeduction;

            $totalGross += $gross;
            $totalDeductions += $absentDeduction + $socialSecurity + $irt;
            $totalNet += $net;

            $payslipsData[] = [
                'payroll_run_id'  => $run->id,
                'employee_id'     => $employee->id,
                'gross_salary'    => round($gross, 2),
                'base_salary'     => round($baseSalary, 2),
                'overtime_amount' => round($overtimeAmount, 2),
                'overtime_hours'  => round($overtimeHours, 2),
                'absent_days'     => round($absentDays, 2),
                'absent_deduction'=> round($absentDeduction, 2),
                'social_security' => round($socialSecurity, 2),
                'irt_amount'      => round($irt, 2),
                'net_salary'      => round($net, 2),
                'status'          => 'rascunho',
            ];
        }

        $this->payrollRepository->createPayslips($payslipsData);

        $run->update([
            'employee_count'  => count($payslipsData),
            'total_gross'     => round($totalGross, 2),
            'total_deductions'=> round($totalDeductions, 2),
            'total_net'       => round($totalNet, 2),
            'status'          => 'processado',
        ]);

        return ['run' => $run, 'payslips' => $this->getPayslipsByRun((int) $run->id)];
    }
}