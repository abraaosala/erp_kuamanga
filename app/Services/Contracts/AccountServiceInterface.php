<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\AccountPlan;
use Illuminate\Support\Collection;

interface AccountServiceInterface
{
    public function getFullChart(int $empresaId): Collection;
    
    public function getAccountById(int $id): ?AccountPlan;
    
    public function createAccount(int $empresaId, array $data): AccountPlan;
    
    public function updateAccount(int $id, array $data): bool;
    
    public function deleteAccount(int $id): bool;

    public function getAccountsByEmpresa(int $empresaId): \Illuminate\Support\Collection;

    public function createJournalEntry(int $empresaId, array $data): \App\Models\JournalEntry;

    public function getJournalEntries(int $empresaId): \Illuminate\Support\Collection;

    public function getLedger(int $empresaId, ?int $accountId = null, ?string $startDate = null, ?string $endDate = null): array;
    
    public function getTrialBalance(int $empresaId, ?string $startDate = null, ?string $endDate = null): array;

    public function getDashboardMetrics(int $empresaId, int $year, int $month): array;

    public function getBalanceSheet(int $empresaId, ?string $endDate = null): array;
    
    public function getIncomeStatement(int $empresaId, ?string $startDate = null, ?string $endDate = null): array;
}
