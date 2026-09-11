<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Services\Contracts\BenefitServiceInterface;
use App\Services\Contracts\DepartmentServiceInterface;
use App\Services\Contracts\PositionServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class BenefitController
{
    public const CATEGORIES = [
        'alimentacao' => 'Alimentação',
        'educacao'    => 'Educação',
        'saude'       => 'Saúde',
        'transporte'  => 'Transporte',
        'outro'       => 'Outro',
    ];

    public function __construct(
        protected BenefitServiceInterface $benefitService,
        protected DepartmentServiceInterface $departmentService,
        protected PositionServiceInterface $positionService,
        protected BladeOne $blade,
        protected Validator $validator,
    ) {}

    public function index(Request $request): Response
    {
        $perPageRaw = $request->get('perPage', 15);
        /** @var int $perPage */
        $perPage = is_numeric($perPageRaw) ? (int) $perPageRaw : 15;
        $searchRaw = $request->get('search');
        /** @var string|null $search */
        $search = is_string($searchRaw) ? $searchRaw : null;

        $benefits = $this->benefitService->paginate($perPage, $search);
        $summary = $this->benefitService->summary();
        $assignedCounts = $this->benefitService->assignedCounts();
        $categories = self::CATEGORIES;

        $html = $this->blade->run('rh.benefits.index', [
            'benefits'       => $benefits,
            'summary'        => $summary,
            'assignedCounts' => $assignedCounts,
            'categories'     => $categories,
            'search'         => $search,
            'perPage'        => $perPage,
            'success'        => $_SESSION['flash_success'] ?? null,
            'error'          => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request): Response
    {
        $departments = $this->departmentService->getAll();
        $positions = $this->positionService->getAll();

        $html = $this->blade->run('rh.benefits.create', [
            'departments' => $departments,
            'positions'   => $positions,
            'categories'  => self::CATEGORIES,
            'error'       => $_SESSION['flash_error'] ?? null,
            'old'         => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['flash_error'], $_SESSION['old']);

        return response($html);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        $validation = $this->validator->make($data, [
            'name'              => 'required|max:120',
            'description'       => 'nullable|max:2000',
            'category'          => 'nullable|in:alimentacao,educacao,saude,transporte,outro',
            'position_id'       => 'nullable|integer|exists:positions,id',
            'department_id'     => 'nullable|integer|exists:departments,id',
            'min_tenure_months' => 'nullable|integer|min:0',
            'status'            => 'required|in:active,inactive',
        ], [
            'name.required'            => 'O nome do benefício é obrigatório.',
            'name.max'                 => 'O nome do benefício não pode exceder 120 caracteres.',
            'position_id.exists'       => 'Cargo selecionado não existe.',
            'department_id.exists'     => 'Departamento selecionado não existe.',
            'min_tenure_months.min'    => 'A antiguidade mínima não pode ser negativa.',
            'status.required'          => 'O estado é obrigatório.',
            'status.in'                => 'Estado inválido.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            $_SESSION['old'] = $request->all();
            return redirect('/rh/benefits/create');
        }

        try {
            $this->benefitService->create($data);
            $_SESSION['flash_success'] = 'Benefício criado com sucesso!';
            return redirect('/rh/benefits');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $_SESSION['old'] = $request->all();
            return redirect('/rh/benefits/create');
        }
    }

    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $benefit = $this->benefitService->getById($id);
        if (!$benefit) {
            $_SESSION['flash_error'] = 'Benefício não encontrado.';
            return redirect('/rh/benefits');
        }

        $assigned = $this->benefitService->employeesByBenefit((int) $benefit->id);
        $eligible = $this->benefitService->eligibleEmployees((int) $benefit->id);

        $html = $this->blade->run('rh.benefits.show', [
            'benefit'      => $benefit,
            'assigned'     => $assigned,
            'eligible'     => $eligible,
            'categories'   => self::CATEGORIES,
            'success'      => $_SESSION['flash_success'] ?? null,
            'error'        => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function edit(Request $request, int $id): Response|RedirectResponse
    {
        $benefit = $this->benefitService->getById($id);
        if (!$benefit) {
            $_SESSION['flash_error'] = 'Benefício não encontrado.';
            return redirect('/rh/benefits');
        }

        $departments = $this->departmentService->getAll();
        $positions = $this->positionService->getAll();

        $html = $this->blade->run('rh.benefits.edit', [
            'benefit'     => $benefit,
            'departments' => $departments,
            'positions'   => $positions,
            'categories'  => self::CATEGORIES,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        $validation = $this->validator->make($data, [
            'name'              => 'required|max:120',
            'description'       => 'nullable|max:2000',
            'category'          => 'nullable|in:alimentacao,educacao,saude,transporte,outro',
            'position_id'       => 'nullable|integer|exists:positions,id',
            'department_id'     => 'nullable|integer|exists:departments,id',
            'min_tenure_months' => 'nullable|integer|min:0',
            'status'            => 'required|in:active,inactive',
        ], [
            'name.required'            => 'O nome do benefício é obrigatório.',
            'name.max'                 => 'O nome do benefício não pode exceder 120 caracteres.',
            'position_id.exists'       => 'Cargo selecionado não existe.',
            'department_id.exists'     => 'Departamento selecionado não existe.',
            'min_tenure_months.min'    => 'A antiguidade mínima não pode ser negativa.',
            'status.required'          => 'O estado é obrigatório.',
            'status.in'                => 'Estado inválido.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/benefits/' . $id . '/edit');
        }

        try {
            $this->benefitService->update($id, $data);
            $_SESSION['flash_success'] = 'Benefício atualizado com sucesso!';
            return redirect('/rh/benefits');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/benefits/' . $id . '/edit');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->benefitService->delete($id);
            $_SESSION['flash_success'] = 'Benefício removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/benefits');
    }

    public function assign(Request $request, int $id): RedirectResponse
    {
        $data = array_map(fn($v) => $v === '' ? null : $v, $request->all());

        $validation = $this->validator->make($data, [
            'employee_id' => 'required|integer|exists:employees,id',
            'started_at'  => 'nullable|date',
            'notes'       => 'nullable|max:1000',
        ], [
            'employee_id.required' => 'O funcionário é obrigatório.',
            'employee_id.exists'   => 'Funcionário selecionado não existe.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/benefits/' . $id . '#' . $this->anchorAssigned());
        }

        /** @var int $employeeId */
        $employeeId = (int) $data['employee_id'];
        /** @var string|null $startedAt */
        $startedAt = is_string($data['started_at'] ?? null) ? $data['started_at'] : null;
        /** @var string|null $notes */
        $notes = is_string($data['notes'] ?? null) ? $data['notes'] : null;

        try {
            $this->benefitService->assign($id, $employeeId, $startedAt, $notes);
            $_SESSION['flash_success'] = 'Benefício atribuído ao funcionário!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/benefits/' . $id . '#' . $this->anchorAssigned());
    }

    public function unassign(int $id, int $employeeId): RedirectResponse
    {
        try {
            $this->benefitService->unassign($id, $employeeId);
            $_SESSION['flash_success'] = 'Benefício removido do funcionário.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/benefits/' . $id . '#' . $this->anchorAssigned());
    }

    private function anchorAssigned(): string
    {
        return 'funcionarios';
    }
}
