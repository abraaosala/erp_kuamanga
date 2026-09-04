<?php

declare(strict_types=1);

namespace App\Services\Modules\Accounting;

use App\Models\AccountPlan;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Services\Contracts\AccountServiceInterface;
use Illuminate\Support\Collection;

class AccountService implements AccountServiceInterface
{
    public function __construct(protected AccountRepositoryInterface $accountRepository) {}

    public function getFullChart(int $empresaId): Collection
    {
        return $this->accountRepository->getHierarchy($empresaId);
    }

    public function getAccountById(int $id): ?AccountPlan
    {
        return $this->accountRepository->findById($id);
    }

    public function createAccount(int $empresaId, array $data): AccountPlan
    {
        $data['empresa_id'] = $empresaId;
        
        $parentId = $data['parent_id'] ?? null;
        if ($parentId !== null && is_numeric($parentId)) {
            $parent = $this->accountRepository->findById((int) $parentId);
            if (!$parent || $parent->empresa_id !== $empresaId) {
                throw new \InvalidArgumentException('Parent account invalid.');
            }
        }
        
        return $this->accountRepository->create($data);
    }

    public function updateAccount(int $id, array $data): bool
    {
        return $this->accountRepository->update($id, $data);
    }

    public function deleteAccount(int $id): bool
    {
        $account = $this->accountRepository->findById($id);
        if ($account && $account->children()->count() > 0) {
            throw new \LogicException('Cannot delete an account that has sub-accounts.');
        }
        
        return $this->accountRepository->delete($id);
    }

    public function getAccountsByEmpresa(int $empresaId): Collection
    {
        return $this->accountRepository->allByEmpresa($empresaId);
    }

    public function createJournalEntry(int $empresaId, array $data): \App\Models\JournalEntry
    {
        if (!isset($data['items']) || !is_array($data['items']) || count($data['items']) < 2) {
            throw new \InvalidArgumentException('O lançamento deve conter pelo menos duas linhas.');
        }

        return $this->accountRepository->transaction(function () use ($empresaId, $data) {
            $totalDebit = 0.0;
            $totalCredit = 0.0;

            /** @var array<int, array<string, mixed>> $items */
            $items = $data['items'];

            foreach ($items as $item) {
                $itemType = $item['type'] ?? '';
                $itemAmount = $item['amount'] ?? 0;
                if ($itemType === 'debit') {
                    $totalDebit += is_numeric($itemAmount) ? (float) $itemAmount : 0.0;
                } else {
                    $totalCredit += is_numeric($itemAmount) ? (float) $itemAmount : 0.0;
                }
            }

            if (abs($totalDebit - $totalCredit) > 0.001) {
                throw new \LogicException("Journal entry is not balanced. Total Debit: {$totalDebit}, Total Credit: {$totalCredit}");
            }

            /** @var JournalEntry $entry */
            $entry = JournalEntry::create([
                'empresa_id'  => $empresaId,
                'date'        => $data['date'],
                'description' => $data['description'],
                'reference'   => $data['reference'] ?? null,
            ]);

            foreach ($items as $item) {
                $entry->items()->create([
                    'account_id' => $item['account_id'],
                    'type'       => $item['type'],
                    'amount'     => $item['amount'],
                ]);
            }

            return $entry;
        });
    }

    public function getJournalEntries(int $empresaId): Collection
    {
        /** @var \Illuminate\Support\Collection<int, \App\Models\JournalEntry> $result */
        $result = JournalEntry::with(['items.account'])
            ->where('empresa_id', $empresaId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return $result;
    }

    public function getLedger(int $empresaId, ?int $accountId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = JournalItem::with(['entry', 'account'])
            ->whereHas('entry', function ($q) use ($empresaId, $startDate, $endDate) {
                $q->where('empresa_id', $empresaId);
                if ($startDate) $q->whereDate('date', '>=', $startDate);
                if ($endDate) $q->whereDate('date', '<=', $endDate);
            });

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $items = $query->join('journal_entries', 'journal_items.entry_id', '=', 'journal_entries.id')
            ->select('journal_items.*', 'journal_entries.date')
            ->orderBy('journal_entries.date', 'asc')
            ->orderBy('journal_items.id', 'asc')
            ->get();

        $runningBalance = 0.0;
        $ledger = [];

        foreach ($items as $item) {
            /** @var \stdClass $item */
            $itemType = is_string($item->type) ? $item->type : '';
            $itemAmount = is_numeric($item->amount) ? (float) $item->amount : 0.0;

            if ($itemType === 'debit') {
                $runningBalance += $itemAmount;
            } else {
                $runningBalance -= $itemAmount;
            }

            $item->running_balance = $runningBalance;
            $ledger[] = $item;
        }

        return $ledger;
    }

    public function getTrialBalance(int $empresaId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = JournalItem::whereHas('entry', function ($q) use ($empresaId, $startDate, $endDate) {
                $q->where('empresa_id', $empresaId);
                if ($startDate) $q->whereDate('date', '>=', $startDate);
                if ($endDate) $q->whereDate('date', '<=', $endDate);
            });

        $items = $query->selectRaw('account_id, type, SUM(amount) as total')
            ->groupBy('account_id', 'type')
            ->get();

        $balances = [];
        foreach ($items as $item) {
            /** @var \stdClass $item */
            $itemAccountId = $item->account_id;

            if (!isset($balances[$itemAccountId])) {
                $balances[$itemAccountId] = [
                    'debit' => 0.0,
                    'credit' => 0.0,
                ];
            }

            $itemType = is_string($item->type) ? $item->type : '';
            $itemTotal = is_numeric($item->total) ? (float) $item->total : 0.0;

            if ($itemType === 'debit') {
                $balances[$itemAccountId]['debit'] += $itemTotal;
            } else {
                $balances[$itemAccountId]['credit'] += $itemTotal;
            }
        }
        
        $accounts = $this->getAccountsByEmpresa($empresaId)->keyBy('id');
        
        $trialBalance = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;
        
        foreach ($balances as $accountId => $totals) {
            $account = $accounts->get($accountId);
            if (!$account) continue;
            
            $balance = $totals['debit'] - $totals['credit'];
            
            $finalDebit = $balance > 0 ? $balance : 0.0;
            $finalCredit = $balance < 0 ? abs($balance) : 0.0;
            
            $trialBalance[] = [
                'account' => $account,
                'total_debit' => $totals['debit'],
                'total_credit' => $totals['credit'],
                'final_debit' => $finalDebit,
                'final_credit' => $finalCredit,
            ];
            
            $totalDebit += $finalDebit;
            $totalCredit += $finalCredit;
        }
        
        usort($trialBalance, function ($a, $b) {
            /** @var \App\Models\AccountPlan $aAccount */
            $aAccount = $a['account'];
            /** @var \App\Models\AccountPlan $bAccount */
            $bAccount = $b['account'];
            return strcmp($aAccount->code, $bAccount->code);
        });

        return [
            'items' => $trialBalance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ];
    }

    public function getDashboardMetrics(int $empresaId, int $year, int $month): array
    {
        $items = JournalItem::join('journal_entries', 'journal_items.entry_id', '=', 'journal_entries.id')
            ->join('account_plans', 'journal_items.account_id', '=', 'account_plans.id')
            ->where('journal_entries.empresa_id', $empresaId)
            ->whereYear('journal_entries.date', $year)
            ->selectRaw('
                account_plans.code,
                account_plans.name,
                journal_items.type,
                journal_items.amount,
                MONTH(journal_entries.date) as month
            ')
            ->get();

        $metrics = [
            'total_assets' => 0.0,
            'total_liabilities' => 0.0,
            'revenue_month' => 0.0,
            'expenses_month' => 0.0,
            'net_income_month' => 0.0,
            'monthly_revenue' => array_fill(1, 12, 0.0),
            'monthly_expenses' => array_fill(1, 12, 0.0),
            'expense_breakdown' => [],
        ];

        foreach ($items as $item) {
            /** @var \stdClass $item */
            $code = is_string($item->code) ? $item->code : '';
            $class = $code !== '' ? substr($code, 0, 1) : false;
            if ($class === false) {
                continue;
            }

            $amount = is_numeric($item->amount) ? (float) $item->amount : 0.0;
            $m = is_numeric($item->month) ? (int) $item->month : 0;
            if ($m < 1 || $m > 12) {
                continue;
            }
            
            $itemType = is_string($item->type) ? $item->type : '';
            $isDebit = $itemType === 'debit';
            
            if (in_array($class, ['1', '2', '3', '4'])) {
                $metrics['total_assets'] += $isDebit ? $amount : -$amount;
            } elseif (in_array($class, ['5', '8'])) {
                $metrics['total_liabilities'] += !$isDebit ? $amount : -$amount;
            } elseif ($class === '6') {
                $expenseVal = $isDebit ? $amount : -$amount;
                $metrics['monthly_expenses'][$m] += $expenseVal;
                
                if ($m === $month) {
                    $metrics['expenses_month'] += $expenseVal;
                    $groupCode = substr($code, 0, 2);
                    if (!isset($metrics['expense_breakdown'][$groupCode])) {
                        $metrics['expense_breakdown'][$groupCode] = [
                            'name' => 'Conta ' . $groupCode,
                            'value' => 0.0,
                        ];
                    }
                    $metrics['expense_breakdown'][$groupCode]['value'] += $expenseVal;
                }
            } elseif ($class === '7') {
                $revVal = !$isDebit ? $amount : -$amount;
                $metrics['monthly_revenue'][$m] += $revVal;
                
                if ($m === $month) {
                    $metrics['revenue_month'] += $revVal;
                }
            }
        }
        
        $metrics['net_income_month'] = $metrics['revenue_month'] - $metrics['expenses_month'];
        
        $metrics['expense_breakdown'] = array_values($metrics['expense_breakdown']);

        return $metrics;
    }

    public function getBalanceSheet(int $empresaId, ?string $endDate = null): array
    {
        $query = JournalItem::join('journal_entries', 'journal_items.entry_id', '=', 'journal_entries.id')
            ->join('account_plans', 'journal_items.account_id', '=', 'account_plans.id')
            ->where('journal_entries.empresa_id', $empresaId)
            ->selectRaw('
                account_plans.code,
                account_plans.name,
                journal_items.type,
                journal_items.amount
            ');

        if ($endDate) {
            $query->whereDate('journal_entries.date', '<=', $endDate);
        }

        $items = $query->get();

        $assets = [];
        $liabilitiesAndEquity = [];
        $totalAssets = 0.0;
        $totalLiabilitiesAndEquity = 0.0;
        
        $netIncome = 0.0;

        foreach ($items as $item) {
            /** @var \stdClass $item */
            $code = is_string($item->code) ? $item->code : '';
            $class = $code !== '' ? substr($code, 0, 1) : false;
            if ($class === false) {
                continue;
            }

            $group2 = substr($code, 0, 2);

            $amount = is_numeric($item->amount) ? (float) $item->amount : 0.0;
            $itemType = is_string($item->type) ? $item->type : '';
            $isDebit = $itemType === 'debit';

            if ($class === '6') {
                $netIncome -= $isDebit ? $amount : -$amount;
            } elseif ($class === '7') {
                $netIncome += !$isDebit ? $amount : -$amount;
            }

            if (in_array($class, ['1', '2', '3', '4'])) {
                if (!isset($assets[$group2])) {
                    $assets[$group2] = ['code' => $group2, 'name' => 'Conta ' . $group2, 'balance' => 0.0];
                }
                $val = $isDebit ? $amount : -$amount;
                $assets[$group2]['balance'] += $val;
                $totalAssets += $val;
            } 
            elseif (in_array($class, ['5', '8'])) {
                if (!isset($liabilitiesAndEquity[$group2])) {
                    $liabilitiesAndEquity[$group2] = ['code' => $group2, 'name' => 'Conta ' . $group2, 'balance' => 0.0];
                }
                $val = !$isDebit ? $amount : -$amount;
                $liabilitiesAndEquity[$group2]['balance'] += $val;
                $totalLiabilitiesAndEquity += $val;
            }
        }

        $totalLiabilitiesAndEquity += $netIncome;

        ksort($assets);
        ksort($liabilitiesAndEquity);

        return [
            'assets' => array_values($assets),
            'liabilities_and_equity' => array_values($liabilitiesAndEquity),
            'total_assets' => $totalAssets,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            'net_income' => $netIncome
        ];
    }

    public function getIncomeStatement(int $empresaId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = JournalItem::join('journal_entries', 'journal_items.entry_id', '=', 'journal_entries.id')
            ->join('account_plans', 'journal_items.account_id', '=', 'account_plans.id')
            ->where('journal_entries.empresa_id', $empresaId)
            ->whereIn(\Illuminate\Support\Facades\DB::raw('SUBSTRING(account_plans.code, 1, 1)'), ['6', '7'])
            ->selectRaw('
                account_plans.code,
                account_plans.name,
                journal_items.type,
                journal_items.amount
            ');

        if ($startDate) {
            $query->whereDate('journal_entries.date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('journal_entries.date', '<=', $endDate);
        }

        $items = $query->get();

        $revenues = [];
        $expenses = [];
        $totalRevenues = 0.0;
        $totalExpenses = 0.0;

        foreach ($items as $item) {
            /** @var \stdClass $item */
            $code = is_string($item->code) ? $item->code : '';
            $class = $code !== '' ? substr($code, 0, 1) : false;
            if ($class === false) {
                continue;
            }

            $group2 = substr($code, 0, 2);

            $amount = is_numeric($item->amount) ? (float) $item->amount : 0.0;
            $itemType = is_string($item->type) ? $item->type : '';
            $isDebit = $itemType === 'debit';

            if ($class === '7') {
                if (!isset($revenues[$group2])) {
                    $revenues[$group2] = ['code' => $group2, 'name' => 'Conta ' . $group2, 'balance' => 0.0];
                }
                $val = !$isDebit ? $amount : -$amount;
                $revenues[$group2]['balance'] += $val;
                $totalRevenues += $val;
            } elseif ($class === '6') {
                if (!isset($expenses[$group2])) {
                    $expenses[$group2] = ['code' => $group2, 'name' => 'Conta ' . $group2, 'balance' => 0.0];
                }
                $val = $isDebit ? $amount : -$amount;
                $expenses[$group2]['balance'] += $val;
                $totalExpenses += $val;
            }
        }

        ksort($revenues);
        ksort($expenses);

        return [
            'revenues' => array_values($revenues),
            'expenses' => array_values($expenses),
            'total_revenues' => $totalRevenues,
            'total_expenses' => $totalExpenses,
            'net_income' => $totalRevenues - $totalExpenses
        ];
    }
}
