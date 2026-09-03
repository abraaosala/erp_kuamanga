<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\WorkSchedule;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use App\Services\Contracts\WorkScheduleServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WorkScheduleService implements WorkScheduleServiceInterface
{
    public function __construct(
        protected WorkScheduleRepositoryInterface $workScheduleRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->workScheduleRepository->all();
    }

    public function getById(int $id): ?WorkSchedule
    {
        return $this->workScheduleRepository->findById($id);
    }

    public function create(array $data): WorkSchedule
    {
        return $this->workScheduleRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->workScheduleRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->workScheduleRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->workScheduleRepository->paginate($perPage, $search);
    }
}
