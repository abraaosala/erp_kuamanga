<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class AttendanceController
{
    public function __construct(
        protected AttendanceServiceInterface $attendanceService,
        protected EmployeeServiceInterface $employeeService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request)
    {
        $search  = $request->get('search');
        $perPage = (int) $request->get('perPage', 15);
        $records = $this->attendanceService->paginate($perPage, $search);

        $html = $this->blade->run('rh.attendance.index', [
            'records' => $records,
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

        $html = $this->blade->run('rh.attendance.create', [
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
            'check_in'     => 'nullable|date_format:H:i',
            'check_out'    => 'nullable|date_format:H:i',
            'status'       => 'required|in:presente,atrasado,falta,justificado',
            'observations' => 'nullable|max:1000',
        ], [
            'employee_id.required' => 'O funcionário é obrigatório.',
            'employee_id.exists'   => 'Funcionário selecionado não existe.',
            'date.required'        => 'A data é obrigatória.',
            'status.required'      => 'O status é obrigatório.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/attendance/create');
        }

        try {
            $this->attendanceService->create($data);
            $_SESSION['flash_success'] = 'Registo de ponto cadastrado com sucesso!';
            return redirect('/rh/attendance');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/attendance/create');
        }
    }

    public function edit(Request $request, int $id)
    {
        $record = $this->attendanceService->getById($id);

        if (!$record) {
            $_SESSION['flash_error'] = 'Registo não encontrado.';
            return redirect('/rh/attendance');
        }

        $employees = $this->employeeService->getAll();

        $html = $this->blade->run('rh.attendance.edit', [
            'record'    => $record,
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
            'check_in'     => 'nullable|date_format:H:i',
            'check_out'    => 'nullable|date_format:H:i',
            'status'       => 'required|in:presente,atrasado,falta,justificado',
            'observations' => 'nullable|max:1000',
        ], [
            'employee_id.required' => 'O funcionário é obrigatório.',
            'employee_id.exists'   => 'Funcionário selecionado não existe.',
            'date.required'        => 'A data é obrigatória.',
            'status.required'      => 'O status é obrigatório.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/attendance/' . $id . '/edit');
        }

        try {
            $this->attendanceService->update($id, $data);
            $_SESSION['flash_success'] = 'Registo de ponto atualizado com sucesso!';
            return redirect('/rh/attendance');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/attendance/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->attendanceService->delete($id);
            $_SESSION['flash_success'] = 'Registo removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/attendance');
    }
}
