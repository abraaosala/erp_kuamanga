<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\PayrollServiceInterface;
use App\Support\ValorExtenso;
use Dompdf\Dompdf;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class PayrollController
{
    public function __construct(
        protected PayrollServiceInterface $payrollService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request): Response
    {
        $perPageRaw = $request->get('perPage', 15);
        /** @var int $perPage */
        $perPage   = is_numeric($perPageRaw) ? (int) $perPageRaw : 15;
        $searchRaw = $request->get('search');
        /** @var string|null $search */
        $search    = is_string($searchRaw) ? $searchRaw : null;
        $runs      = $this->payrollService->paginate($perPage, $search);

        $html = $this->blade->run('rh.payroll.index', [
            'runs'    => $runs,
            'search'  => $search,
            'perPage' => $perPage,
            'success' => $_SESSION['flash_success'] ?? null,
            'error'   => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request): Response
    {
        $html = $this->blade->run('rh.payroll.create', [
            'error' => $_SESSION['flash_error'] ?? null,
            'hasEligibleEmployees' => $this->payrollService->hasEligibleEmployees(),
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        $validation = $this->validator->make($data, [
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'description'  => 'nullable|max:255',
        ], [
            'period_start.required'    => 'A data de início do período é obrigatória.',
            'period_end.required'      => 'A data de fim do período é obrigatória.',
            'period_end.after_or_equal' => 'A data de fim deve ser igual ou posterior à data de início.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/payroll/create');
        }

        /** @var string $periodStart */
        $periodStart = $data['period_start'];
        /** @var string $periodEnd */
        $periodEnd = $data['period_end'];
        /** @var string $description */
        $description = is_string($data['description']) ? $data['description'] : '';

        try {
            $this->payrollService->runFromContracts($periodStart, $periodEnd, $description);
            $_SESSION['flash_success'] = 'Folha salarial gerada com sucesso!';
            return redirect('/rh/payroll');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/payroll/create');
        }
    }

    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $run = $this->payrollService->getById($id);

        if (!$run) {
            $_SESSION['flash_error'] = 'Folha salarial não encontrada.';
            return redirect('/rh/payroll');
        }

        $payslips = $this->payrollService->getPayslipsByRun($id);

        $html = $this->blade->run('rh.payroll.show', [
            'run'      => $run,
            'payslips' => $payslips,
            'success'  => $_SESSION['flash_success'] ?? null,
            'error'    => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $this->payrollService->delete($id);
            $_SESSION['flash_success'] = 'Folha salarial removida com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/payroll');
    }

    public function recibo(int $payslipId): Response|RedirectResponse
    {
        $payslip = $this->payrollService->getPayslipById($payslipId);

        if (!$payslip) {
            $_SESSION['flash_error'] = 'Recibo não encontrado.';
            return redirect('/rh/payroll');
        }

        $valorExtenso = (new ValorExtenso())->money((float) $payslip->net_salary);

        $html = $this->blade->run('rh.payroll.recibo', [
            'payslip'     => $payslip,
            'valorExtenso'=> $valorExtenso,
        ]);

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        $fileName = 'recibo-vencimento-' . $payslip->employee->name . '-' . date('Ymd') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}