<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Services\Contracts\AttendanceServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AttendanceService implements AttendanceServiceInterface
{
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->attendanceRepository->all();
    }

    public function getById(int $id): ?Attendance
    {
        return $this->attendanceRepository->findById($id);
    }

    public function create(array $data): Attendance
    {
        return $this->attendanceRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->attendanceRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->attendanceRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->attendanceRepository->paginate($perPage, $search);
    }
}
