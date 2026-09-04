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
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var Collection<int, Attendance> $result */
        $result = Attendance::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->get();

        return $result;
    }

    public function findById(int $id): ?Attendance
    {
        /** @var \App\Models\Attendance|null $attendance */
        $attendance = Attendance::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->find($id);

        return $attendance;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Attendance
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Attendance::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
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
