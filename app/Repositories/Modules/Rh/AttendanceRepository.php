<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    protected function empresaId(): int
    {
        return current_empresa()->id;
    }

    public function all(): Collection
    {
        return Attendance::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->get();
    }

    public function findById(int $id): ?Attendance
    {
        return Attendance::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->find($id);
    }

    public function create(array $data): Attendance
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Attendance::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $attendance = $this->findById($id);
        if (!$attendance) {
            return false;
        }
        return $attendance->update($data);
    }

    public function delete(int $id): bool
    {
        $attendance = $this->findById($id);
        if (!$attendance) {
            return false;
        }
        return (bool) $attendance->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = Attendance::with('employee')
            ->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        return $q->orderBy('date', 'desc')->paginate($perPage);
    }
}
