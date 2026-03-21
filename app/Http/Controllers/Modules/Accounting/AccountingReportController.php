<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Accounting;

use App\Services\Contracts\AccountServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;

class AccountingReportController
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected BladeOne $blade
    ) {}

    public function ledger(Request $request)
    {
        // Must have an active enterprise
        $empresaId = session()->empresaId();
        if (!$empresaId) {
            session()->flash('error', 'Selecione uma empresa primeiro.');
            return redirect('/dashboard');
        }

        $accountId = $request->input('account_id');
        if (is_numeric($accountId)) {
            $accountId = (int)$accountId;
        } else {
            $accountId = null;
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch all accounts for the dropdown
        $accounts = $this->accountService->getAccountsByEmpresa($empresaId);
        
        // Fetch ledger data
        $ledger = $this->accountService->getLedger($empresaId, $accountId, $startDate, $endDate);
        
        // Find selected account if any
        $selectedAccount = $accountId ? $accounts->firstWhere('id', $accountId) : null;

        $html = $this->blade->run('accounting.reports.ledger', [
            'title' => 'Razão Geral',
            'accounts' => $accounts,
            'ledger' => $ledger,
            'selectedAccountId' => $accountId,
            'selectedAccount' => $selectedAccount,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return response($html);
    }

    public function trialBalance(Request $request)
    {
        $empresaId = session()->empresaId();
        if (!$empresaId) {
            session()->flash('error', 'Selecione uma empresa primeiro.');
            return redirect('/dashboard');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $report = $this->accountService->getTrialBalance($empresaId, $startDate, $endDate);

        $html = $this->blade->run('accounting.reports.trial_balance', [
            'title' => 'Balancetes',
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return response($html);
    }

    public function balanceSheet(Request $request)
    {
        $empresaId = session()->empresaId();
        if (!$empresaId) {
            session()->flash('error', 'Selecione uma empresa.');
            return redirect('/dashboard');
        }

        $endDate = $request->input('end_date', date('Y-12-31'));

        $data = $this->accountService->getBalanceSheet($empresaId, $endDate);

        $html = $this->blade->run('accounting.reports.balance_sheet', [
            'title' => 'Balanço Patrimonial',
            'endDate' => $endDate,
            'data' => $data
        ]);

        return response($html);
    }

    public function incomeStatement(Request $request)
    {
        $empresaId = session()->empresaId();
        if (!$empresaId) {
            session()->flash('error', 'Selecione uma empresa.');
            return redirect('/dashboard');
        }

        $year = date('Y');
        $startDate = $request->input('start_date', "$year-01-01");
        $endDate = $request->input('end_date', "$year-12-31");

        $data = $this->accountService->getIncomeStatement($empresaId, $startDate, $endDate);

        $html = $this->blade->run('accounting.reports.income_statement', [
            'title' => 'Demonstração de Resultados',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $data
        ]);

        return response($html);
    }
}
