<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\DepartmentServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class DepartmentController
{
    public function __construct(
        protected DepartmentServiceInterface $departmentService,
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(Request $request): Response
    {
        $perPageRaw  = $request->get('perPage', 15);
        /** @var int $perPage */
        $perPage     = is_numeric($perPageRaw) ? (int) $perPageRaw : 15;
        $searchRaw   = $request->get('search');
        /** @var string|null $search */
        $search      = is_string($searchRaw) ? $searchRaw : null;
        $departments = $this->departmentService->paginate($perPage, $search);

        $html = $this->blade->run('rh.departments.index', [
            'departments' => $departments,
            'search'      => $search,
            'perPage'     => $perPage,
            'success'     => $_SESSION['flash_success'] ?? null,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request): Response
    {
        $html = $this->blade->run('rh.departments.create', [
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'        => 'required|min:2|max:100',
            'description' => 'nullable|max:255',
        ], [
            'name.required' => 'O nome do departamento é obrigatório.',
            'name.min'      => 'O nome deve ter pelo menos 2 caracteres.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/departments/create');
        }

        try {
            $this->departmentService->create($data);
            $_SESSION['flash_success'] = 'Departamento criado com sucesso!';
            return redirect('/rh/departments');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/departments/create');
        }
    }

    public function edit(Request $request, int $id): Response|RedirectResponse
    {
        $department = $this->departmentService->getById($id);

        if (!$department) {
            $_SESSION['flash_error'] = 'Departamento não encontrado.';
            return redirect('/rh/departments');
        }

        $html = $this->blade->run('rh.departments.edit', [
            'department' => $department,
            'error'      => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'        => 'required|min:2|max:100',
            'description' => 'nullable|max:255',
        ], [
            'name.required' => 'O nome do departamento é obrigatório.',
            'name.min'      => 'O nome deve ter pelo menos 2 caracteres.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/departments/' . $id . '/edit');
        }

        try {
            $this->departmentService->update($id, $data);
            $_SESSION['flash_success'] = 'Departamento atualizado com sucesso!';
            return redirect('/rh/departments');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/departments/' . $id . '/edit');
        }
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $this->departmentService->delete($id);
            $_SESSION['flash_success'] = 'Departamento removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/departments');
    }
}
