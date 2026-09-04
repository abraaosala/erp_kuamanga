<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Accounting;

use App\Core\Session;
use App\Services\Contracts\AccountServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;

class AccountingReportController
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected BladeOne $blade
    ) {}

    public function ledger(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        /** @var Session $session */
        $session = session();
        $empresaId = $session->empresaId();
        if (!$empresaId) {
            $session->flash('error', 'Selecione uma empresa primeiro.');
            return redirect('/dashboard');
        }

        $accountIdValue = $request->input('account_id');
        $accountId = is_numeric($accountIdValue) ? (int) $accountIdValue : null;

        $startDateValue = $request->input('start_date');
        $startDate = is_string($startDateValue) && $startDateValue !== '' ? $startDateValue : null;

        $endDateValue = $request->input('end_date');
        $endDate = is_string($endDateValue) && $endDateValue !== '' ? $endDateValue : null;

        $accounts = $this->accountService->getAccountsByEmpresa($empresaId);

        $ledger = $this->accountService->getLedger($empresaId, $accountId, $startDate, $endDate);

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

    public function trialBalance(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        /** @var Session $session */
        $session = session();
        $empresaId = $session->empresaId();
        if (!$empresaId) {
            $session->flash('error', 'Selecione uma empresa primeiro.');
            return redirect('/dashboard');
        }

        $startDateValue = $request->input('start_date');
        $startDate = is_string($startDateValue) && $startDateValue !== '' ? $startDateValue : null;

        $endDateValue = $request->input('end_date');
        $endDate = is_string($endDateValue) && $endDateValue !== '' ? $endDateValue : null;

        $report = $this->accountService->getTrialBalance($empresaId, $startDate, $endDate);

        $html = $this->blade->run('accounting.reports.trial_balance', [
            'title' => 'Balancetes',
            'report' => $report,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return response($html);
    }

    public function balanceSheet(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        /** @var Session $session */
        $session = session();
        $empresaId = $session->empresaId();
        if (!$empresaId) {
            $session->flash('error', 'Selecione uma empresa.');
            return redirect('/dashboard');
        }

        $endDateValue = $request->input('end_date', date('Y-12-31'));
        $endDate = is_string($endDateValue) && $endDateValue !== '' ? $endDateValue : null;

        $data = $this->accountService->getBalanceSheet($empresaId, $endDate);

        $html = $this->blade->run('accounting.reports.balance_sheet', [
            'title' => 'Balanço Patrimonial',
            'endDate' => $endDate,
            'data' => $data
        ]);

        return response($html);
    }

    public function incomeStatement(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        /** @var Session $session */
        $session = session();
        $empresaId = $session->empresaId();
        if (!$empresaId) {
            $session->flash('error', 'Selecione uma empresa.');
            return redirect('/dashboard');
        }

        $year = date('Y');

        $startDateValue = $request->input('start_date', "$year-01-01");
        $startDate = is_string($startDateValue) && $startDateValue !== '' ? $startDateValue : null;

        $endDateValue = $request->input('end_date', "$year-12-31");
        $endDate = is_string($endDateValue) && $endDateValue !== '' ? $endDateValue : null;

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
