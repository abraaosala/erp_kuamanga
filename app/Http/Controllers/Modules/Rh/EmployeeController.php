<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\DepartmentServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\PositionServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class EmployeeController
{
    public function __construct(
        protected EmployeeServiceInterface $employeeService,
        protected DepartmentServiceInterface $departmentService,
        protected PositionServiceInterface $positionService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request)
    {
        $search  = $request->get('search');
        $perPage = (int) $request->get('perPage', 15);
        $employees = $this->employeeService->paginate($perPage, $search);
        $html = $this->blade->run('rh.employees.index', [
            'employees' => $employees,
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
        $departments = $this->departmentService->getAll();
        $positions   = $this->positionService->getAll();

        $html = $this->blade->run('rh.employees.create', [
            'departments' => $departments,
            'positions'   => $positions,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'          => 'required|min:2|max:150',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|max:30',
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id'   => 'nullable|integer|exists:positions,id',
            'hire_date'     => 'nullable|date',
        ], [
            'name.required'          => 'O nome do funcionário é obrigatório.',
            'name.min'               => 'O nome deve ter pelo menos 2 caracteres.',
            'email.email'            => 'Email inválido.',
            'hire_date.date'         => 'Data de admissão inválida.',
            'department_id.exists'   => 'Departamento selecionado não existe.',
            'position_id.exists'     => 'Cargo selecionado não existe.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/employees/create');
        }

        try {
            $this->employeeService->create($data);
            $_SESSION['flash_success'] = 'Funcionário cadastrado com sucesso!';
            return redirect('/rh/employees');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/employees/create');
        }
    }

    public function edit(Request $request, int $id)
    {
        $employee = $this->employeeService->getById($id);


        if (!$employee) {
            $_SESSION['flash_error'] = 'Funcionário não encontrado.';
            return redirect('/rh/employees');
        }

        $departments = $this->departmentService->getAll();
        $positions   = $this->positionService->getAll();

        $html = $this->blade->run('rh.employees.edit', [
            'employee'    => $employee,
            'departments' => $departments,
            'positions'   => $positions,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $validation = $this->validator->make($data, [
            'name'          => 'required|min:2|max:150',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|max:30',
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id'   => 'nullable|integer|exists:positions,id',
            'hire_date'     => 'nullable|date',
        ], [
            'name.required'          => 'O nome do funcionário é obrigatório.',
            'name.min'               => 'O nome deve ter pelo menos 2 caracteres.',
            'email.email'            => 'Email inválido.',
            'hire_date.date'         => 'Data de admissão inválida.',
            'department_id.exists'   => 'Departamento selecionado não existe.',
            'position_id.exists'     => 'Cargo selecionado não existe.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/employees/' . $id . '/edit');
        }

        try {
            $this->employeeService->update($id, $data);
            $_SESSION['flash_success'] = 'Funcionário atualizado com sucesso!';
            return redirect('/rh/employees');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/employees/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->employeeService->delete($id);
            $_SESSION['flash_success'] = 'Funcionário removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/employees');
    }
}
