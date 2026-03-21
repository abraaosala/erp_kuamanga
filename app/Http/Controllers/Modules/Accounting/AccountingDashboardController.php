<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Accounting;

use App\Services\Contracts\AccountServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;

class AccountingDashboardController
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected BladeOne $blade
    ) {}

    public function index(Request $request)
    {
        $empresaId = session()->empresaId();
        if (!$empresaId) {
            session()->flash('error', 'Selecione uma empresa para aceder à Contabilidade.');
            return redirect('/dashboard');
        }

        $year = (int)$request->input('year', date('Y'));
        $month = (int)$request->input('month', date('n'));

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
