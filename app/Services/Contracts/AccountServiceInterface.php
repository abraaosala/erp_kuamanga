<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\AccountPlan;
use Illuminate\Support\Collection;

interface AccountServiceInterface
{
    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\AccountPlan>
     */
    public function getFullChart(int $empresaId): Collection;

    public function getAccountById(int $id): ?AccountPlan;

    /**
     * @param array<string, mixed> $data
     */
    public function createAccount(int $empresaId, array $data): AccountPlan;

    /**
     * @param array<string, mixed> $data
     */
    public function updateAccount(int $id, array $data): bool;

    public function deleteAccount(int $id): bool;

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\AccountPlan>
     */
    public function getAccountsByEmpresa(int $empresaId): \Illuminate\Support\Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function createJournalEntry(int $empresaId, array $data): \App\Models\JournalEntry;

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\JournalEntry>
     */
    public function getJournalEntries(int $empresaId): \Illuminate\Support\Collection;

    /**
     * @return array<int, mixed>
     */
    public function getLedger(int $empresaId, ?int $accountId = null, ?string $startDate = null, ?string $endDate = null): array;

    /**
     * @return array<string, mixed>
     */
    public function getTrialBalance(int $empresaId, ?string $startDate = null, ?string $endDate = null): array;

    /**
     * @return array<string, mixed>
     */
    public function getDashboardMetrics(int $empresaId, int $year, int $month): array;

    /**
     * @return array<string, mixed>
     */
    public function getBalanceSheet(int $empresaId, ?string $endDate = null): array;

    /**
     * @return array<string, mixed>
     */
    public function getIncomeStatement(int $empresaId, ?string $startDate = null, ?string $endDate = null): array;
}
