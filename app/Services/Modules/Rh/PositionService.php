<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Position;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Services\Contracts\PositionServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PositionService implements PositionServiceInterface
{
    public function __construct(
        protected PositionRepositoryInterface $positionRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->positionRepository->all();
    }

    public function getById(int $id): ?Position
    {
        return $this->positionRepository->findById($id);
    }

    public function create(array $data): Position
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('O nome do cargo é obrigatório.');
        }

        return $this->positionRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->positionRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->positionRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->positionRepository->paginate($perPage, $search);
    }

    public function getByDepartment(int $departmentId): Collection
    {
        return $this->positionRepository->findByDepartment($departmentId);
    }
}
