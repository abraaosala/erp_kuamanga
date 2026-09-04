<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\HourBankEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface HourBankEntryServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\HourBankEntry>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?HourBankEntry;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): HourBankEntry;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\HourBankEntry>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function balanceByEmployee(int $employeeId): float;

    /**
     * @return array<int, array{employee: string, balance: float}>
     */
    public function summary(): array;
}
