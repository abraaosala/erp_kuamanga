<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\EmployeeScheduleServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\WorkScheduleServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class EmployeeScheduleController
{
    public function __construct(
        protected EmployeeScheduleServiceInterface $employeeScheduleService,
        protected WorkScheduleServiceInterface $workScheduleService,
        protected EmployeeServiceInterface $employeeService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request, int $scheduleId): Response|RedirectResponse
    {
        $schedule = $this->workScheduleService->getById($scheduleId);

        if (!$schedule) {
            $_SESSION['flash_error'] = 'Escala não encontrada.';
            return redirect('/rh/schedules');
        }

        $employees = $this->employeeScheduleService->getEmployeesBySchedule($scheduleId);

        $html = $this->blade->run('rh.employee_schedules.index', [
            'schedule'  => $schedule,
            'employees' => $employees,
            'success'   => $_SESSION['flash_success'] ?? null,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function assign(Request $request, int $scheduleId): Response|RedirectResponse
    {
        $schedule = $this->workScheduleService->getById($scheduleId);

        if (!$schedule) {
            $_SESSION['flash_error'] = 'Escala não encontrada.';
            return redirect('/rh/schedules');
        }

        $allEmployees = $this->employeeService->getAll();
        $linkedIds = $this->employeeScheduleService->getEmployeesBySchedule($scheduleId)
            ->pluck('id')
            ->toArray();
        $available = $allEmployees->filter(fn($e) => !in_array($e->id, $linkedIds));

        $html = $this->blade->run('rh.employee_schedules.assign', [
            'schedule'   => $schedule,
            'employees'  => $available->values(),
            'error'      => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request, int $scheduleId): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'employee_id' => 'required|integer',
        ], [
            'employee_id.required' => 'Selecione um funcionário.',
            'employee_id.integer'  => 'ID do funcionário inválido.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/schedules/' . $scheduleId . '/employees/assign');
        }

        $meta = [
            'is_default' => (bool) ($data['is_default'] ?? false),
            'start_date' => $data['start_date'] ?? null,
            'end_date'   => $data['end_date'] ?? null,
        ];

        $ok = $this->employeeScheduleService->assign((int) $data['employee_id'], $scheduleId, $meta);

        if ($ok) {
            $_SESSION['flash_success'] = 'Funcionário vinculado à escala com sucesso!';
        } else {
            $_SESSION['flash_error'] = 'Este funcionário já está vinculado a esta escala.';
        }

        return redirect('/rh/schedules/' . $scheduleId . '/employees');
    }

    public function destroy(int $scheduleId, int $employeeId): RedirectResponse
    {
        $ok = $this->employeeScheduleService->remove($employeeId, $scheduleId);

        if ($ok) {
            $_SESSION['flash_success'] = 'Vínculo removido com sucesso!';
        } else {
            $_SESSION['flash_error'] = 'Vínculo não encontrado.';
        }

        return redirect('/rh/schedules/' . $scheduleId . '/employees');
    }

    public function setDefault(int $scheduleId, int $employeeId): RedirectResponse
    {
        $ok = $this->employeeScheduleService->setDefault($employeeId, $scheduleId);

        if ($ok) {
            $_SESSION['flash_success'] = 'Escala principal definida com sucesso!';
        } else {
            $_SESSION['flash_error'] = 'Não foi possível definir a escala principal.';
        }

        return redirect('/rh/schedules/' . $scheduleId . '/employees');
    }
}
