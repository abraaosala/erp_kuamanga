<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\HourBankEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface HourBankEntryServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?HourBankEntry;

    public function create(array $data): HourBankEntry;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function balanceByEmployee(int $employeeId): float;

    public function summary(): array;
}
