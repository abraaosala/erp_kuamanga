<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\HourBankEntry;
use App\Repositories\Contracts\HourBankEntryRepositoryInterface;
use App\Services\Contracts\HourBankEntryServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HourBankEntryService implements HourBankEntryServiceInterface
{
    public function __construct(
        protected HourBankEntryRepositoryInterface $hourBankEntryRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->hourBankEntryRepository->all();
    }

    public function getById(int $id): ?HourBankEntry
    {
        return $this->hourBankEntryRepository->findById($id);
    }

    public function create(array $data): HourBankEntry
    {
        return $this->hourBankEntryRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->hourBankEntryRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->hourBankEntryRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->hourBankEntryRepository->paginate($perPage, $search);
    }

    public function balanceByEmployee(int $employeeId): float
    {
        return $this->hourBankEntryRepository->balanceByEmployee($employeeId);
    }

    public function summary(): array
    {
        return $this->hourBankEntryRepository->summary();
    }
}
