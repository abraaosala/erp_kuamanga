<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\WorkScheduleServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class WorkScheduleController
{
    public function __construct(
        protected WorkScheduleServiceInterface $workScheduleService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request)
    {
        $search      = $request->get('search');
        $perPage     = (int) $request->get('perPage', 15);
        $schedules   = $this->workScheduleService->paginate($perPage, $search);

        $html = $this->blade->run('rh.schedules.index', [
            'schedules' => $schedules,
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
        $html = $this->blade->run('rh.schedules.create', [
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request)
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        if (is_array($data['days_of_week'] ?? null)) {
            $data['days_of_week'] = implode(',', $data['days_of_week']);
        }

        $validation = $this->validator->make($data, [
            'name'            => 'required|min:2|max:100',
            'check_in_time'   => 'nullable|date_format:H:i',
            'check_out_time'  => 'nullable|date_format:H:i',
            'break_minutes'   => 'nullable|integer|min:0|max:600',
        ], [
            'name.required' => 'O nome da escala é obrigatório.',
            'name.min'      => 'O nome deve ter pelo menos 2 caracteres.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/schedules/create');
        }

        try {
            $this->workScheduleService->create($data);
            $_SESSION['flash_success'] = 'Escala criada com sucesso!';
            return redirect('/rh/schedules');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/schedules/create');
        }
    }

    public function edit(Request $request, int $id)
    {
        $schedule = $this->workScheduleService->getById($id);

        if (!$schedule) {
            $_SESSION['flash_error'] = 'Escala não encontrada.';
            return redirect('/rh/schedules');
        }

        $html = $this->blade->run('rh.schedules.edit', [
            'schedule' => $schedule,
            'error'    => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id)
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        if (is_array($data['days_of_week'] ?? null)) {
            $data['days_of_week'] = implode(',', $data['days_of_week']);
        }

        $validation = $this->validator->make($data, [
            'name'            => 'required|min:2|max:100',
            'check_in_time'   => 'nullable|date_format:H:i',
            'check_out_time'  => 'nullable|date_format:H:i',
            'break_minutes'   => 'nullable|integer|min:0|max:600',
        ], [
            'name.required' => 'O nome da escala é obrigatório.',
            'name.min'      => 'O nome deve ter pelo menos 2 caracteres.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/schedules/' . $id . '/edit');
        }

        try {
            $this->workScheduleService->update($id, $data);
            $_SESSION['flash_success'] = 'Escala atualizada com sucesso!';
            return redirect('/rh/schedules');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/schedules/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->workScheduleService->delete($id);
            $_SESSION['flash_success'] = 'Escala removida com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/schedules');
    }
}
