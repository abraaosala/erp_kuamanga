<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\WorkSchedule;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WorkScheduleRepository implements WorkScheduleRepositoryInterface
{
    protected function empresaId(): int
    {
        return current_empresa()->id;
    }

    public function all(): Collection
    {
        return WorkSchedule::where('empresa_id', $this->empresaId())
            ->get();
    }

    public function findById(int $id): ?WorkSchedule
    {
        return WorkSchedule::where('empresa_id', $this->empresaId())
            ->find($id);
    }

    public function create(array $data): WorkSchedule
    {
        $data['empresa_id'] ??= $this->empresaId();
        return WorkSchedule::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $schedule = $this->findById($id);
        if (!$schedule) {
            return false;
        }
        return $schedule->update($data);
    }

    public function delete(int $id): bool
    {
        $schedule = $this->findById($id);
        if (!$schedule) {
            return false;
        }
        return (bool) $schedule->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = WorkSchedule::where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where('name', 'like', "%{$search}%");
        }

        return $q->orderBy('name')->paginate($perPage);
    }
}
