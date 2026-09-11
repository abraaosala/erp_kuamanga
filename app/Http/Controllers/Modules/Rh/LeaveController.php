<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\LeaveServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class LeaveController
{
    public function __construct(
        protected LeaveServiceInterface $leaveService,
        protected EmployeeServiceInterface $employeeService,
        protected BladeOne $blade,
        protected Validator $validator,
    ) {}

    public function index(Request $request): Response
    {
        $perPageRaw = $request->get('perPage', 15);
        /** @var int $perPage */
        $perPage   = is_numeric($perPageRaw) ? (int) $perPageRaw : 15;
        $searchRaw = $request->get('search');
        /** @var string|null $search */
        $search      = is_string($searchRaw) ? $searchRaw : null;
        $requests    = $this->leaveService->paginate($perPage, $search);
        $balances    = $this->leaveService->balances();
        $pendingCount = $this->leaveService->countPending();

        $html = $this->blade->run('rh.leaves.index', [
            'leaveRequests' => $requests,
            'balances'      => $balances,
            'pendingCount'  => $pendingCount,
            'search'        => $search,
            'perPage'       => $perPage,
            'success'       => $_SESSION['flash_success'] ?? null,
            'error'         => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(): Response
    {
        $employees = $this->employeeService->getAll();

        $html = $this->blade->run('rh.leaves.create', [
            'employees' => $employees,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        $validation = $this->validator->make($data, [
            'employee_id' => 'required|integer|exists:employees,id',
            'leave_type'  => 'required|in:ferias,licenca_maternidade,licenca_paternidade,licenca_doenca,licenca_remunerada,licenca_nao_remunerada,falta_justificada',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date',
            'reason'      => 'nullable|max:1000',
        ], [
            'employee_id.required' => 'O funcionário é obrigatório.',
            'employee_id.exists'   => 'Funcionário selecionado não existe.',
            'leave_type.required'  => 'O tipo é obrigatório.',
            'start_date.required'  => 'A data de início é obrigatória.',
            'end_date.required'    => 'A data de fim é obrigatória.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/leaves/create');
        }

        try {
            $this->leaveService->requestLeave($data);
            $_SESSION['flash_success'] = 'Pedido de férias criado com sucesso!';
            return redirect('/rh/leaves');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/leaves/create');
        }
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        /** @var string|null $notes */
        $notes = is_string($request->get('decision_notes')) ? $request->get('decision_notes') : null;

        try {
            $this->leaveService->approve($id, $this->decidedBy(), $notes);
            $_SESSION['flash_success'] = 'Pedido de férias aprovado: férias registadas.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/leaves');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        /** @var string|null $notes */
        $notes = is_string($request->get('decision_notes')) ? $request->get('decision_notes') : null;

        try {
            $this->leaveService->reject($id, $this->decidedBy(), $notes);
            $_SESSION['flash_success'] = 'Pedido de férias rejeitado.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/leaves');
    }

    public function cancel(int $id): RedirectResponse
    {
        try {
            $this->leaveService->cancel($id);
            $_SESSION['flash_success'] = 'Pedido de férias cancelado.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/leaves');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->leaveService->delete($id);
            $_SESSION['flash_success'] = 'Pedido de férias removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/leaves');
    }

    private function decidedBy(): ?int
    {
        /** @var mixed $userId */
        $userId = $_SESSION['user_id'] ?? null;

        return is_numeric($userId) ? (int) $userId : null;
    }
}
