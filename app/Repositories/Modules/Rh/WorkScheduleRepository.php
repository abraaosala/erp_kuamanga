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
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var Collection<int, WorkSchedule> $result */
        $result = WorkSchedule::where('empresa_id', $this->empresaId())
            ->get();

        return $result;
    }

    public function findById(int $id): ?WorkSchedule
    {
        /** @var \App\Models\WorkSchedule|null $schedule */
        $schedule = WorkSchedule::where('empresa_id', $this->empresaId())
            ->find($id);

        return $schedule;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): WorkSchedule
    {
        $data['empresa_id'] ??= $this->empresaId();
        return WorkSchedule::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
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

        return $q->withCount('employees')->orderBy('name')->paginate($perPage);
    }
}
