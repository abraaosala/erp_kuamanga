<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Leave;
use App\Models\LeaveRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LeaveRepositoryInterface
{
    /**
     * @return Collection<int, LeaveRequest>
     */
    public function allRequests(): Collection;

    public function findRequestById(int $id): ?LeaveRequest;

    /**
     * @param array<string, mixed> $data
     */
    public function createRequest(array $data): LeaveRequest;

    /**
     * @param array<string, mixed> $data
     */
    public function updateRequest(int $id, array $data): bool;

    public function deleteRequest(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, LeaveRequest>
     */
    public function paginateRequests(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function countPendingRequests(): int;

    /**
     * @param array<string, mixed> $data
     */
    public function createLeave(array $data): Leave;

    /**
     * Soma dos dias de férias efectivas (type ferias, não canceladas) de um funcionário.
     */
    public function sumLeaveDaysByEmployee(int $employeeId): int;

    public function hasApprovedLeaveOverlapping(int $employeeId, string $startDate, string $endDate): bool;

    public function deleteLeavesByRequest(int $leaveRequestId): bool;
}