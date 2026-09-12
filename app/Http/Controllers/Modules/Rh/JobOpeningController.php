<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Models\JobOpening;
use App\Services\Contracts\DepartmentServiceInterface;
use App\Services\Contracts\JobOpeningServiceInterface;
use App\Services\Contracts\PositionServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class JobOpeningController
{
    public function __construct(
        protected JobOpeningServiceInterface $jobOpeningService,
        protected DepartmentServiceInterface $departmentService,
        protected PositionServiceInterface $positionService,
        protected BladeOne $blade,
        protected Validator $validator,
    ) {}

    public function index(Request $request): Response
    {
        $perPageRaw  = $request->get('perPage', 15);
        /** @var int $perPage */
        $perPage     = is_numeric($perPageRaw) ? (int) $perPageRaw : 15;
        $searchRaw   = $request->get('search');
        /** @var string|null $search */
        $search      = is_string($searchRaw) ? $searchRaw : null;
        $openings    = $this->jobOpeningService->paginate($perPage, $search);

        $html = $this->blade->run('rh.job_openings.index', [
            'openings'  => $openings,
            'search'    => $search,
            'perPage'   => $perPage,
            'summary'   => $this->jobOpeningService->summary(),
            'success'   => $_SESSION['flash_success'] ?? null,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(Request $request): Response
    {
        $html = $this->blade->run('rh.job_openings.create', [
            'departments' => $this->departmentService->getAll(),
            'positions'   => $this->positionService->getAll(),
            'statuses'    => JobOpening::STATUSES,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'title'           => 'required|min:2|max:150',
            'description'     => 'nullable|string',
            'requirements'    => 'nullable|string',
            'openings_count'  => 'nullable|integer|min:1|max:999',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0|gte:salary_range_min',
            'location'        => 'nullable|max:150',
            'closes_at'       => 'nullable|date',
        ], [
            'title.required'           => 'O título da vaga é obrigatório.',
            'salary_range_max.gte'     => 'O teto salarial deve ser maior ou igual ao piso.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/job-openings/create');
        }

        try {
            $this->jobOpeningService->create($data);
            $_SESSION['flash_success'] = 'Vaga criada com sucesso!';
            return redirect('/rh/job-openings');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/job-openings/create');
        }
    }

    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $opening = $this->jobOpeningService->getById($id);

        if (!$opening) {
            $_SESSION['flash_error'] = 'Vaga não encontrada.';
            return redirect('/rh/job-openings');
        }

        $html = $this->blade->run('rh.job_openings.show', [
            'opening'    => $opening,
            'candidates' => $this->jobOpeningService->byOpening($id),
            'statuses'   => JobOpening::STATUSES,
            'success'    => $_SESSION['flash_success'] ?? null,
            'error'      => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function edit(Request $request, int $id): Response|RedirectResponse
    {
        $opening = $this->jobOpeningService->getById($id);

        if (!$opening) {
            $_SESSION['flash_error'] = 'Vaga não encontrada.';
            return redirect('/rh/job-openings');
        }

        $html = $this->blade->run('rh.job_openings.edit', [
            'opening'     => $opening,
            'departments' => $this->departmentService->getAll(),
            'positions'   => $this->positionService->getAll(),
            'statuses'    => JobOpening::STATUSES,
            'error'       => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'title'           => 'required|min:2|max:150',
            'description'     => 'nullable|string',
            'requirements'    => 'nullable|string',
            'openings_count'  => 'nullable|integer|min:1|max:999',
            'salary_range_min' => 'nullable|numeric|min:0',
            'salary_range_max' => 'nullable|numeric|min:0|gte:salary_range_min',
            'location'        => 'nullable|max:150',
            'closes_at'       => 'nullable|date',
        ], [
            'title.required'           => 'O título da vaga é obrigatório.',
            'salary_range_max.gte'     => 'O teto salarial deve ser maior ou igual ao piso.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/job-openings/' . $id . '/edit');
        }

        try {
            $this->jobOpeningService->update($id, $data);
            $_SESSION['flash_success'] = 'Vaga atualizada com sucesso!';
            return redirect('/rh/job-openings');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/job-openings/' . $id . '/edit');
        }
    }

    public function status(Request $request, int $id): RedirectResponse
    {
        $statusRaw = $request->get('status');
        /** @var string|null $status */
        $status = is_string($statusRaw) ? $statusRaw : null;

        try {
            if (!$status || !$this->jobOpeningService->changeStatus($id, $status)) {
                throw new \RuntimeException('Vaga não encontrada.');
            }
            $_SESSION['flash_success'] = 'Estado da vaga atualizado!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/job-openings/' . $id);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $this->jobOpeningService->delete($id);
            $_SESSION['flash_success'] = 'Vaga removida com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/job-openings');
    }
}
