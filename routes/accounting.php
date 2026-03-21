<?php

declare(strict_types=1);

use App\Http\Controllers\Modules\Accounting\AccountController;
use App\Http\Controllers\Modules\Accounting\JournalController;
use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'accounting', 'middleware' => 'auth'], function (Router $router) {
    
    // Accounts (Plano de Contas)
    $router->get('/accounts', [AccountController::class, 'index'])->name('accounting.accounts.index');
    $router->get('/accounts/create', [AccountController::class, 'create'])->name('accounting.accounts.create');
    $router->post('/accounts', [AccountController::class, 'store'])->name('accounting.accounts.store');
    $router->get('/accounts/{id}/edit', [AccountController::class, 'edit'])->name('accounting.accounts.edit');
    $router->post('/accounts/{id}/update', [AccountController::class, 'update'])->name('accounting.accounts.update');
    $router->post('/accounts/{id}/delete', [AccountController::class, 'destroy'])->name('accounting.accounts.destroy');
    // Dashboard
    $router->get('/dashboard', [\App\Http\Controllers\Modules\Accounting\AccountingDashboardController::class, 'index'])->name('accounting.dashboard');

    // Journal Entries (Lançamentos)
    $router->get('/journal', [JournalController::class, 'index'])->name('accounting.journal.index');
    $router->get('/journal/create', [JournalController::class, 'create'])->name('accounting.journal.create');
    $router->post('/journal', [JournalController::class, 'store'])->name('accounting.journal.store');
    // Accounting Reports
    $router->get('/reports/ledger', [\App\Http\Controllers\Modules\Accounting\AccountingReportController::class, 'ledger'])->name('accounting.reports.ledger');
    $router->get('/reports/trial-balance', [\App\Http\Controllers\Modules\Accounting\AccountingReportController::class, 'trialBalance'])->name('accounting.reports.trial-balance');
    
    // Mapas Oficiais
    $router->get('/reports/balance-sheet', [\App\Http\Controllers\Modules\Accounting\AccountingReportController::class, 'balanceSheet'])->name('accounting.reports.balance-sheet');
    $router->get('/reports/income-statement', [\App\Http\Controllers\Modules\Accounting\AccountingReportController::class, 'incomeStatement'])->name('accounting.reports.income-statement');
});
