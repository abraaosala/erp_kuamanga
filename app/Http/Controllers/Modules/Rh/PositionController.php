<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\DepartmentServiceInterface;
use App\Services\Contracts\PositionServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class PositionController
{
    public function __construct(
        protected PositionServiceInterface $positionService,
        protected DepartmentServiceInterface $departmentService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request)
    {
        $search  = $request->get('search');
        $perPage = (int) $request->get('perPage', 15);
        $positions = $this->positionService->paginate($perPage, $search);

        $html = $this->blade->run('rh.positions.index', [
            'positions' => $positions,
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

        $html = $this->blade->run('rh.positions.create', [
            'departments' => $departments,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'            => 'required|min:2|max:100',
            'description'     => 'nullable|max:255',
            'department_id'   => 'nullable|integer|exists:departments,id',
            'salary_range_min'=> 'nullable|numeric|min:0',
            'salary_range_max'=> 'nullable|numeric|min:0',
        ], [
            'name.required'          => 'O nome do cargo é obrigatório.',
            'name.min'               => 'O nome deve ter pelo menos 2 caracteres.',
            'department_id.exists'   => 'Departamento selecionado não existe.',
            'salary_range_min.numeric' => 'O salário mínimo deve ser um valor numérico.',
            'salary_range_max.numeric' => 'O salário máximo deve ser um valor numérico.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/positions/create');
        }

        try {
            $this->positionService->create($data);
            $_SESSION['flash_success'] = 'Cargo criado com sucesso!';
            return redirect('/rh/positions');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/positions/create');
        }
    }

    public function edit(Request $request, int $id)
    {
        $position = $this->positionService->getById($id);

        if (!$position) {
            $_SESSION['flash_error'] = 'Cargo não encontrado.';
            return redirect('/rh/positions');
        }

        $departments = $this->departmentService->getAll();

        $html = $this->blade->run('rh.positions.edit', [
            'position'    => $position,
            'departments' => $departments,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'            => 'required|min:2|max:100',
            'description'     => 'nullable|max:255',
            'department_id'   => 'nullable|integer|exists:departments,id',
            'salary_range_min'=> 'nullable|numeric|min:0',
            'salary_range_max'=> 'nullable|numeric|min:0',
        ], [
            'name.required'          => 'O nome do cargo é obrigatório.',
            'name.min'               => 'O nome deve ter pelo menos 2 caracteres.',
            'department_id.exists'   => 'Departamento selecionado não existe.',
            'salary_range_min.numeric' => 'O salário mínimo deve ser um valor numérico.',
            'salary_range_max.numeric' => 'O salário máximo deve ser um valor numérico.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/positions/' . $id . '/edit');
        }

        try {
            $this->positionService->update($id, $data);
            $_SESSION['flash_success'] = 'Cargo atualizado com sucesso!';
            return redirect('/rh/positions');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/positions/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->positionService->delete($id);
            $_SESSION['flash_success'] = 'Cargo removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/positions');
    }
}
