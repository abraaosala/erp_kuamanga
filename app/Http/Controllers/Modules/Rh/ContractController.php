<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\ContractServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class ContractController
{
    public function __construct(
        protected ContractServiceInterface $contractService,
        protected EmployeeServiceInterface $employeeService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request)
    {
        $search  = $request->get('search');
        $perPage = (int) $request->get('perPage', 15);
        $contracts = $this->contractService->paginate($perPage, $search);

        $html = $this->blade->run('rh.contracts.index', [
            'contracts' => $contracts,
            'search'    => $search,
            'perPage'   => $perPage,
            'success'   => $_SESSION['flash_success'] ?? null,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request)
    {
        $employees = $this->employeeService->getAll();

        $html = $this->blade->run('rh.contracts.create', [
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
            'employee_id'   => 'required|integer|exists:employees,id',
            'tipo_contrato' => 'required|max:50',
            'data_inicio'   => 'required|date',
            'data_fim'      => 'nullable|date|after_or_equal:data_inicio',
            'salario_base'  => 'nullable|numeric|min:0',
            'carga_horaria' => 'nullable|max:50',
            'observacoes'   => 'nullable|max:1000',
        ], [
            'employee_id.required'   => 'O funcionário é obrigatório.',
            'employee_id.exists'     => 'Funcionário selecionado não existe.',
            'tipo_contrato.required' => 'O tipo de contrato é obrigatório.',
            'data_inicio.required'   => 'A data de início é obrigatória.',
            'data_fim.after_or_equal' => 'A data de fim deve ser igual ou posterior à data de início.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/contracts/create');
        }

        try {
            $this->contractService->create($data);
            $_SESSION['flash_success'] = 'Contrato cadastrado com sucesso!';
            return redirect('/rh/contracts');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/contracts/create');
        }
    }

    public function edit(Request $request, int $id)
    {
        $contract = $this->contractService->getById($id);

        if (!$contract) {
            $_SESSION['flash_error'] = 'Contrato não encontrado.';
            return redirect('/rh/contracts');
        }

        $employees = $this->employeeService->getAll();

        $html = $this->blade->run('rh.contracts.edit', [
            'contract'  => $contract,
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
            'employee_id'   => 'required|integer|exists:employees,id',
            'tipo_contrato' => 'required|max:50',
            'data_inicio'   => 'required|date',
            'data_fim'      => 'nullable|date|after_or_equal:data_inicio',
            'salario_base'  => 'nullable|numeric|min:0',
            'carga_horaria' => 'nullable|max:50',
            'observacoes'   => 'nullable|max:1000',
        ], [
            'employee_id.required'   => 'O funcionário é obrigatório.',
            'employee_id.exists'     => 'Funcionário selecionado não existe.',
            'tipo_contrato.required' => 'O tipo de contrato é obrigatório.',
            'data_inicio.required'   => 'A data de início é obrigatória.',
            'data_fim.after_or_equal' => 'A data de fim deve ser igual ou posterior à data de início.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/contracts/' . $id . '/edit');
        }

        try {
            $this->contractService->update($id, $data);
            $_SESSION['flash_success'] = 'Contrato atualizado com sucesso!';
            return redirect('/rh/contracts');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/contracts/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->contractService->delete($id);
            $_SESSION['flash_success'] = 'Contrato removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/contracts');
    }
}
