<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Rotation;
use App\Models\ScheduledShift;
use App\Repositories\Contracts\RosterRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RosterRepository implements RosterRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    /**
     * @return Collection<int, Rotation>
     */
    public function allRotations(): Collection
    {
        /** @var Collection<int, Rotation> $result */
        $result = Rotation::where('empresa_id', $this->empresaId())
            ->withCount('scheduledShifts')
            ->orderByDesc('created_at')
            ->get();

        return $result;
    }

    public function findRotationById(int $id): ?Rotation
    {
        /** @var \App\Models\Rotation|null $rotation */
        $rotation = Rotation::where('empresa_id', $this->empresaId())
            ->find($id);

        return $rotation;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createRotation(array $data): Rotation
    {
        $data['empresa_id'] ??= $this->empresaId();

        return Rotation::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateRotation(int $id, array $data): bool
    {
        $rotation = $this->findRotationById($id);
        if (!$rotation) {
            return false;
        }

        return $rotation->update($data);
    }

    public function deleteRotation(int $id): bool
    {
        $rotation = $this->findRotationById($id);
        if (!$rotation) {
            return false;
        }

        return (bool) $rotation->delete();
    }

    public function paginateRotations(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = Rotation::where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where('name', 'like', "%{$search}%");
        }

        return $q->withCount('scheduledShifts')->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * @return Collection<int, ScheduledShift>
     */
    public function shiftsBetween(string $start, string $end, ?int $rotationId = null): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Builder<ScheduledShift> $q */
        $q = ScheduledShift::with(['employee', 'workSchedule', 'rotation'])
            ->where('empresa_id', $this->empresaId())
            ->whereBetween('date', [$start, $end]);

        if ($rotationId !== null) {
            $q->where('rotation_id', $rotationId);
        }

        /** @var Collection<int, ScheduledShift> $shifts */
        $shifts = $q->orderBy('date')->orderBy('employee_id')->get();

        return $shifts;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function insertShifts(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        foreach ($rows as &$row) {
            $row['empresa_id'] ??= $this->empresaId();
            $row['created_at'] ??= date('Y-m-d H:i:s');
            $row['updated_at'] ??= date('Y-m-d H:i:s');
        }
        unset($row);

        DB::table('scheduled_shifts')->insert($rows);
    }

    public function deleteShiftsByRotation(int $rotationId): int
    {
        return (int) DB::table('scheduled_shifts')
            ->where('empresa_id', $this->empresaId())
            ->where('rotation_id', $rotationId)
            ->whereNull('deleted_at')
            ->delete();
    }

    /**
     * @param list<int> $employeeIds
     */
    public function deleteGeneratedBetween(string $start, string $end, array $employeeIds = []): int
    {
        $query = DB::table('scheduled_shifts')
            ->where('empresa_id', $this->empresaId())
            ->whereBetween('date', [$start, $end])
            ->where('source', 'GERADA')
            ->whereNull('deleted_at');

        if ($employeeIds !== []) {
            $query->whereIn('employee_id', $employeeIds);
        }

        return (int) $query->delete();
    }

    public function deleteShift(int $shiftId): bool
    {
        $shift = ScheduledShift::where('empresa_id', $this->empresaId())->find($shiftId);
        if (!$shift) {
            return false;
        }

        return (bool) $shift->delete();
    }

    /**
     * @return array{
     *     today_work: int,
     *     today_off: int,
     *     total_shifts: int,
     *     rotations_count: int,
     *     employees_covered: int
     * }
     */
    public function stats(string $today): array
    {
        $empresaId = $this->empresaId();

        return [
            'today_work'        => (int) ScheduledShift::where('empresa_id', $empresaId)
                ->whereDate('date', $today)
                ->where('classification', 'TRABALHO')
                ->count(),
            'today_off'         => (int) ScheduledShift::where('empresa_id', $empresaId)
                ->whereDate('date', $today)
                ->where('classification', 'FOLGA')
                ->count(),
            'total_shifts'      => (int) ScheduledShift::where('empresa_id', $empresaId)->count(),
            'rotations_count'   => (int) Rotation::where('empresa_id', $empresaId)->count(),
            'employees_covered' => (int) ScheduledShift::where('empresa_id', $empresaId)
                ->distinct()
                ->count('employee_id'),
        ];
    }
}
