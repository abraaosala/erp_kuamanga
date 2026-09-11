<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\LeaveRepositoryInterface;
use App\Services\Contracts\LeaveServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LeaveService implements LeaveServiceInterface
{
    protected LeavePolicy $leavePolicy;

    public function __construct(
        protected LeaveRepositoryInterface $leaveRepository,
        protected EmployeeRepositoryInterface $employeeRepository
    ) {
        $this->leavePolicy = new LeavePolicy();
    }

    public function getAll(): Collection
    {
        return $this->leaveRepository->allRequests();
    }

    public function getById(int $id): ?LeaveRequest
    {
        return $this->leaveRepository->findRequestById($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function requestLeave(array $data): LeaveRequest
    {
        if (!is_numeric($data['employee_id'] ?? null)) {
            throw new \InvalidArgumentException('Funcionário inválido.');
        }
        $employeeId = (int) $data['employee_id'];

        $leaveType = is_string($data['leave_type'] ?? null) && $data['leave_type'] !== ''
            ? $data['leave_type']
            : LeaveRequest::TYPE_FERIAS;
        $start = is_string($data['start_date'] ?? null) ? $data['start_date'] : '';
        $end = is_string($data['end_date'] ?? null) ? $data['end_date'] : '';
        $reason = is_string($data['reason'] ?? null) && $data['reason'] !== '' ? $data['reason'] : null;

        $days = $this->leavePolicy->inclusiveDays($start, $end);

        if ($this->leaveRepository->hasApprovedLeaveOverlapping($employeeId, $start, $end)) {
            throw new \RuntimeException(
                'O funcionário já possui férias aprovadas num período que se sobrepõe ao pedido.'
            );
        }

        if ($leaveType === LeaveRequest::TYPE_FERIAS) {
            $this->assertEnoughBalance($employeeId, $days);
        }

        return $this->leaveRepository->createRequest([
            'employee_id' => $employeeId,
            'leave_type'  => $leaveType,
            'start_date'  => $start,
            'end_date'    => $end,
            'days'        => $days,
            'reason'      => $reason,
            'status'      => LeaveRequest::STATUS_PENDENTE,
        ]);
    }

    public function approve(int $id, ?int $decidedBy = null, ?string $notes = null): LeaveRequest
    {
        $request = $this->findDecidable($id, 'aprovados');

        if ($request->leave_type === LeaveRequest::TYPE_FERIAS) {
            $this->assertEnoughBalance((int) $request->employee_id, (int) $request->days);
        }

        $this->leaveRepository->updateRequest($id, [
            'status'         => LeaveRequest::STATUS_APROVADO,
            'decided_by'     => $decidedBy,
            'decided_at'     => date('Y-m-d H:i:s'),
            'decision_notes' => $notes,
        ]);

        $this->leaveRepository->createLeave([
            'employee_id'     => $request->employee_id,
            'leave_request_id'=> $id,
            'leave_type'      => $request->leave_type,
            'start_date'      => $request->start_date->format('Y-m-d'),
            'end_date'        => $request->end_date->format('Y-m-d'),
            'days'            => $request->days,
            'status'          => Leave::STATUS_GOZADA,
            'observations'    => $notes,
        ]);

        return $this->requireById($id);
    }

    public function reject(int $id, ?int $decidedBy = null, ?string $notes = null): LeaveRequest
    {
        $this->findDecidable($id, 'rejeitados');

        $this->leaveRepository->updateRequest($id, [
            'status'         => LeaveRequest::STATUS_REJEITADO,
            'decided_by'     => $decidedBy,
            'decided_at'     => date('Y-m-d H:i:s'),
            'decision_notes' => $notes,
        ]);

        return $this->requireById($id);
    }

    public function cancel(int $id): LeaveRequest
    {
        $this->findDecidable($id, 'cancelados');

        $this->leaveRepository->updateRequest($id, [
            'status' => LeaveRequest::STATUS_CANCELADO,
        ]);

        return $this->requireById($id);
    }

    public function delete(int $id): bool
    {
        $request = $this->getById($id);
        if (!$request) {
            return false;
        }

        $this->leaveRepository->deleteLeavesByRequest($id);

        return $this->leaveRepository->deleteRequest($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->leaveRepository->paginateRequests($perPage, $search);
    }

    public function countPending(): int
    {
        return $this->leaveRepository->countPendingRequests();
    }

    /**
     * @return array{entitled: int, used: int, available: int}
     */
    public function balanceByEmployee(int $employeeId): array
    {
        $employee = $this->employeeRepository->findById($employeeId);
        if (!$employee) {
            throw new \RuntimeException('Funcionário não encontrado.');
        }

        return $this->balanceFor($employee);
    }

    /**
     * @return array<int, array{employee: string, entitled: int, used: int, available: int}>
     */
    public function balances(): array
    {
        $result = [];
        /** @var Collection<int, Employee> $employees */
        $employees = $this->employeeRepository->all();
        foreach ($employees as $employee) {
            /** @var Employee $employee */
            $result[] = $this->balanceFor($employee) + ['employee' => $employee->name];
        }

        usort($result, fn (array $a, array $b): int => strcmp($a['employee'], $b['employee']));

        return $result;
    }

    private function assertEnoughBalance(int $employeeId, int $days): void
    {
        $balance = $this->balanceByEmployee($employeeId);
        if ($days > $balance['available']) {
            throw new \RuntimeException(
                sprintf(
                    'Saldo de férias insuficiente: dispõe de %d dia(s) e o pedido é de %d dia(s).',
                    $balance['available'],
                    $days
                )
            );
        }
    }

    private function requireById(int $id): LeaveRequest
    {
        $request = $this->getById($id);
        if (!$request) {
            throw new \RuntimeException('Pedido de férias não encontrado.');
        }

        return $request;
    }

    private function findDecidable(int $id, string $action): LeaveRequest
    {
        $request = $this->getById($id);
        if (!$request) {
            throw new \RuntimeException('Pedido de férias não encontrado.');
        }
        if ($request->status !== LeaveRequest::STATUS_PENDENTE) {
            throw new \RuntimeException('Apenas pedidos pendentes podem ser ' . $action . '.');
        }

        return $request;
    }

    /**
     * @return array{entitled: int, used: int, available: int}
     */
    private function balanceFor(Employee $employee): array
    {
        $hireDate = $employee->hire_date?->format('Y-m-d');
        $entitled = $hireDate !== null ? $this->leavePolicy->entitledDaysFor($hireDate) : 0;
        $used = $this->leaveRepository->sumLeaveDaysByEmployee((int) $employee->id);
        $available = max(0, $entitled - $used);

        return [
            'entitled' => $entitled,
            'used'     => $used,
            'available'=> $available,
        ];
    }
}