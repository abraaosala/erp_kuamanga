<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Contract;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Services\Contracts\ContractServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContractService implements ContractServiceInterface
{
    public function __construct(
        protected ContractRepositoryInterface $contractRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->contractRepository->all();
    }

    public function getById(int $id): ?Contract
    {
        return $this->contractRepository->findById($id);
    }

    public function create(array $data): Contract
    {
        if (empty($data['employee_id'])) {
            throw new \InvalidArgumentException('O funcionário é obrigatório.');
        }
        if (empty($data['tipo_contrato'])) {
            throw new \InvalidArgumentException('O tipo de contrato é obrigatório.');
        }
        if (empty($data['data_inicio'])) {
            throw new \InvalidArgumentException('A data de início é obrigatória.');
        }

        return $this->contractRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->contractRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->contractRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->contractRepository->paginate($perPage, $search);
    }

    public function getByEmployee(int $employeeId): Collection
    {
        return $this->contractRepository->findByEmployee($employeeId);
    }
}
