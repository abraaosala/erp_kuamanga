<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\HourBankEntryServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class HourBankEntryController
{
    public function __construct(
        protected HourBankEntryServiceInterface $hourBankEntryService,
        protected EmployeeServiceInterface $employeeService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request)
    {
        $search  = $request->get('search');
        $perPage = (int) $request->get('perPage', 15);
        $entries = $this->hourBankEntryService->paginate($perPage, $search);
        $summary = $this->hourBankEntryService->summary();

        $html = $this->blade->run('rh.hour_bank.index', [
            'entries' => $entries,
            'summary' => $summary,
            'search'  => $search,
            'perPage' => $perPage,
            'success' => $_SESSION['flash_success'] ?? null,
            'error'   => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request)
    {
        $employees = $this->employeeService->getAll();

        $html = $this->blade->run('rh.hour_bank.create', [
            'employees' => $employees,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request)
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        $validation = $this->validator->make($data, [
            'employee_id'  => 'required|integer|exists:employees,id',
            'date'         => 'required|date',
            'hours'        => 'required|numeric',
            'type'         => 'required|in:horas_extra,compensacao,ajuste,saldo_inicial',
            'observations' => 'nullable|max:1000',
        ], [
            'employee_id.required' => 'O funcionário é obrigatório.',
            'employee_id.exists'   => 'Funcionário selecionado não existe.',
            'date.required'        => 'A data é obrigatória.',
            'hours.required'       => 'As horas são obrigatórias.',
            'type.required'        => 'O tipo é obrigatório.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/hour-bank/create');
        }

        try {
            $this->hourBankEntryService->create($data);
            $_SESSION['flash_success'] = 'Lançamento no banco de horas criado com sucesso!';
            return redirect('/rh/hour-bank');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/hour-bank/create');
        }
    }

    public function edit(Request $request, int $id)
    {
        $entry = $this->hourBankEntryService->getById($id);

        if (!$entry) {
            $_SESSION['flash_error'] = 'Lançamento não encontrado.';
            return redirect('/rh/hour-bank');
        }

        $employees = $this->employeeService->getAll();

        $html = $this->blade->run('rh.hour_bank.edit', [
            'entry'     => $entry,
            'employees' => $employees,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id)
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        $validation = $this->validator->make($data, [
            'employee_id'  => 'required|integer|exists:employees,id',
            'date'         => 'required|date',
            'hours'        => 'required|numeric',
            'type'         => 'required|in:horas_extra,compensacao,ajuste,saldo_inicial',
            'observations' => 'nullable|max:1000',
        ], [
            'employee_id.required' => 'O funcionário é obrigatório.',
            'employee_id.exists'   => 'Funcionário selecionado não existe.',
            'date.required'        => 'A data é obrigatória.',
            'hours.required'       => 'As horas são obrigatórias.',
            'type.required'        => 'O tipo é obrigatório.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/hour-bank/' . $id . '/edit');
        }

        try {
            $this->hourBankEntryService->update($id, $data);
            $_SESSION['flash_success'] = 'Lançamento atualizado com sucesso!';
            return redirect('/rh/hour-bank');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/hour-bank/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->hourBankEntryService->delete($id);
            $_SESSION['flash_success'] = 'Lançamento removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/hour-bank');
    }
}
