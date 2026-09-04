<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Accounting;

use App\Core\Session;
use App\Services\Contracts\AccountServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;

class AccountingDashboardController
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected BladeOne $blade
    ) {}

    public function index(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        /** @var Session $session */
        $session = session();
        $empresaId = $session->empresaId();
        if (!$empresaId) {
            $session->flash('error', 'Selecione uma empresa para aceder à Contabilidade.');
            return redirect('/dashboard');
        }

        $yearValue = $request->input('year', date('Y'));
        $year = is_numeric($yearValue) ? (int) $yearValue : (int) date('Y');

        $monthValue = $request->input('month', date('n'));
        $month = is_numeric($monthValue) ? (int) $monthValue : (int) date('n');

        $metrics = $this->accountService->getDashboardMetrics($empresaId, $year, $month);

        $html = $this->blade->run('accounting.dashboard', [
            'title' => 'Dashboard Analítico',
            'year' => $year,
            'month' => $month,
            'metrics' => $metrics
        ]);

        return response($html);
    }
}
