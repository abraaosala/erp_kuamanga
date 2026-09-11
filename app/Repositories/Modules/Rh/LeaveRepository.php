<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Repositories\Contracts\LeaveRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LeaveRepository implements LeaveRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function allRequests(): Collection
    {
        /** @var Collection<int, LeaveRequest> $requests */
        $requests = LeaveRequest::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->orderBy('created_at', 'desc')
            ->get();

        return $requests;
    }

    public function findRequestById(int $id): ?LeaveRequest
    {
        /** @var \App\Models\LeaveRequest|null $request */
        $request = LeaveRequest::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->find($id);

        return $request;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createRequest(array $data): LeaveRequest
    {
        $data['empresa_id'] ??= $this->empresaId();
        return LeaveRequest::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateRequest(int $id, array $data): bool
    {
        $request = $this->findRequestById($id);
        if (!$request) {
            return false;
        }
        return $request->update($data);
    }

    public function deleteRequest(int $id): bool
    {
        $request = $this->findRequestById($id);
        if (!$request) {
            return false;
        }
        return (bool) $request->delete();
    }

    public function paginateRequests(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = LeaveRequest::with('employee')
            ->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        return $q->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function countPendingRequests(): int
    {
        /** @var int $count */
        $count = LeaveRequest::where('empresa_id', $this->empresaId())
            ->where('status', LeaveRequest::STATUS_PENDENTE)
            ->count();

        return $count;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createLeave(array $data): Leave
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Leave::create($data);
    }

    public function sumLeaveDaysByEmployee(int $employeeId): int
    {
        /** @var mixed $rawSum */
        $rawSum = Leave::where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->where('leave_type', Leave::TYPE_FERIAS)
            ->where('status', '!=', Leave::STATUS_CANCELADA)
            ->sum('days');

        return is_numeric($rawSum) ? (int) $rawSum : 0;
    }

    public function hasApprovedLeaveOverlapping(int $employeeId, string $startDate, string $endDate): bool
    {
        return Leave::where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->where('status', '!=', Leave::STATUS_CANCELADA)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->exists();
    }

    public function deleteLeavesByRequest(int $leaveRequestId): bool
    {
        /** @var int $deleted */
        $deleted = Leave::where('empresa_id', $this->empresaId())
            ->where('leave_request_id', $leaveRequestId)
            ->delete();

        return $deleted > 0;
    }
}
