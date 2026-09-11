<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\EmployeeScheduleServiceInterface;
use App\Services\Contracts\RosterServiceInterface;
use App\Services\Contracts\WorkScheduleServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class RosterController
{
    public function __construct(
        protected RosterServiceInterface $rosterService,
        protected WorkScheduleServiceInterface $workScheduleService,
        protected EmployeeScheduleServiceInterface $employeeScheduleService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request): Response
    {
        $rotations = $this->rosterService->getAllRotations();
        $schedules = $this->workScheduleService->getAll();
        $stats = $this->rosterService->stats(date('Y-m-d'));

        $html = $this->blade->run('rh.rosters.index', [
            'rotations' => $rotations,
            'schedules' => $schedules,
            'stats'     => $stats,
            'success'   => $_SESSION['flash_success'] ?? null,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function events(Request $request): Response
    {
        /** @var mixed $startRaw */
        $startRaw = $request->get('start', '');
        /** @var mixed $endRaw */
        $endRaw = $request->get('end', '');
        /** @var mixed $rotationRaw */
        $rotationRaw = $request->get('rotation_id', '');
        $start = is_string($startRaw) ? $startRaw : '';
        $end   = is_string($endRaw) ? $endRaw : '';
        $rotationId = is_string($rotationRaw) && ctype_digit($rotationRaw)
            ? (int) $rotationRaw
            : null;

        $events = $this->rosterService->events($start, $end, $rotationId);

        return response(json_encode($events, JSON_THROW_ON_ERROR), 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function employees(Request $request, int $scheduleId): Response
    {
        $employees = $this->employeeScheduleService->getEmployeesBySchedule($scheduleId);

        return response(json_encode([
            'employees' => $employees->map(fn ($e) => [
                'id'   => $e->id,
                'name' => $e->name . ($e->department ? ' — ' . $e->department->name : ''),
            ]),
        ], JSON_THROW_ON_ERROR), 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'          => 'required|min:2|max:100',
            'work_schedule_id' => 'required|integer',
            'pattern'       => 'required|array|min:1',
            'pattern.*'     => 'required|integer|min:1|max:60',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ], [
            'name.required'             => 'O nome da rotação é obrigatório.',
            'work_schedule_id.required' => 'Selecione uma escala.',
            'pattern.required'          => 'Informe o padrão do ciclo.',
            'start_date.required'       => 'Informe a data de início.',
            'end_date.required'         => 'Informe a data de fim.',
            'end_date.after_or_equal'   => 'A data de fim deve ser maior ou igual à data de início.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/rosters');
        }

        try {
            $this->rosterService->generate($data);
            $_SESSION['flash_success'] = 'Rotação gerada com sucesso!';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/rosters');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ok = $this->rosterService->deleteRotation($id);

        if ($ok) {
            $_SESSION['flash_success'] = 'Rotação removida com sucesso!';
        } else {
            $_SESSION['flash_error'] = 'Rotação não encontrada.';
        }

        return redirect('/rh/rosters');
    }
}