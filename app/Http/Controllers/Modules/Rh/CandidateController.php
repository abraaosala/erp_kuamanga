<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Models\Candidate;
use App\Services\Contracts\CandidateServiceInterface;
use App\Services\Contracts\JobOpeningServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Factory as Validator;

class CandidateController
{
    public function __construct(
        protected CandidateServiceInterface $candidateService,
        protected JobOpeningServiceInterface $jobOpeningService,
        protected BladeOne $blade,
        protected Validator $validator,
    ) {}

    public function index(Request $request): Response
    {
        $perPageRaw = $request->get('perPage', 15);
        /** @var int $perPage */
        $perPage    = is_numeric($perPageRaw) ? (int) $perPageRaw : 15;
        $searchRaw  = $request->get('search');
        /** @var string|null $search */
        $search     = is_string($searchRaw) ? $searchRaw : null;
        $statusRaw  = $request->get('status');
        /** @var string|null $status */
        $status     = is_string($statusRaw) ? $statusRaw : null;
        $openingRaw = $request->get('job_opening_id');
        /** @var int|null $jobOpeningId */
        $jobOpeningId = is_numeric($openingRaw) ? (int) $openingRaw : null;

        $candidates = $this->candidateService->paginate($perPage, $search, $status, $jobOpeningId);

        $html = $this->blade->run('rh.candidates.index', [
            'candidates'  => $candidates,
            'counts'      => $this->candidateService->countByStatus(),
            'summary'     => $this->candidateService->summary(),
            'openings'    => $this->jobOpeningService->getAll(),
            'filterStatus' => $status,
            'filterOpening' => $jobOpeningId,
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
        $html = $this->blade->run('rh.candidates.create', [
            'openings' => $this->jobOpeningService->getAll(),
            'sources'  => Candidate::SOURCES,
            'statuses' => [
                Candidate::STATUS_NOVO,
                Candidate::STATUS_TRIAGEM,
                Candidate::STATUS_ENTREVISTA,
                Candidate::STATUS_APROVADO,
            ],
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'           => 'required|min:2|max:150',
            'email'          => 'required|email|max:190',
            'phone'          => 'nullable|max:30',
            'job_opening_id' => 'nullable|integer',
            'source'         => 'nullable|string|max:40',
            'notes'          => 'nullable|string',
        ], [
            'name.required'  => 'O nome do candidato é obrigatório.',
            'email.required' => 'O email do candidato é obrigatório.',
            'email.email'    => 'O email deve ser válido.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/candidates/create');
        }

        try {
            $this->candidateService->create($data);
            $_SESSION['flash_success'] = 'Candidato registado com sucesso!';
            return redirect('/rh/candidates');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/candidates/create');
        }
    }

    public function show(Request $request, int $id): Response|RedirectResponse
    {
        $candidate = $this->candidateService->getById($id);

        if (!$candidate) {
            $_SESSION['flash_error'] = 'Candidato não encontrado.';
            return redirect('/rh/candidates');
        }

        $html = $this->blade->run('rh.candidates.show', [
            'candidate'  => $candidate,
            'interviews' => $candidate->interviews,
            'openings'   => $this->jobOpeningService->getAll(),
            'success'    => $_SESSION['flash_success'] ?? null,
            'error'      => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function edit(Request $request, int $id): Response|RedirectResponse
    {
        $candidate = $this->candidateService->getById($id);

        if (!$candidate) {
            $_SESSION['flash_error'] = 'Candidato não encontrado.';
            return redirect('/rh/candidates');
        }

        $html = $this->blade->run('rh.candidates.edit', [
            'candidate' => $candidate,
            'openings'  => $this->jobOpeningService->getAll(),
            'sources'   => Candidate::SOURCES,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'name'           => 'required|min:2|max:150',
            'email'          => 'required|email|max:190',
            'phone'          => 'nullable|max:30',
            'job_opening_id' => 'nullable|integer',
            'source'         => 'nullable|string|max:40',
            'notes'          => 'nullable|string',
        ], [
            'name.required'  => 'O nome do candidato é obrigatório.',
            'email.required' => 'O email do candidato é obrigatório.',
            'email.email'    => 'O email deve ser válido.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/candidates/' . $id . '/edit');
        }

        try {
            $this->candidateService->update($id, $data);
            $_SESSION['flash_success'] = 'Candidato atualizado com sucesso!';
            return redirect('/rh/candidates');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/rh/candidates/' . $id . '/edit');
        }
    }

    public function transition(Request $request, int $id): RedirectResponse
    {
        $stageRaw = $request->get('stage');
        /** @var string|null $stage */
        $stage = is_string($stageRaw) ? $stageRaw : null;

        try {
            if (!$stage) {
                throw new \InvalidArgumentException('Fase de destino inválida.');
            }
            $this->candidateService->transition($id, $stage, $this->decidedBy());
            $_SESSION['flash_success'] = 'Candidato avançou na pipeline!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/candidates/' . $id);
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        try {
            $this->candidateService->reject($id, $this->decidedBy());
            $_SESSION['flash_success'] = 'Candidato rejeitado.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/candidates/' . $id);
    }

    public function hire(Request $request, int $id): RedirectResponse
    {
        try {
            $this->candidateService->hire($id, $this->decidedBy());
            $_SESSION['flash_success'] = 'Candidato contratado e registado como funcionário!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/candidates/' . $id);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $this->candidateService->delete($id);
            $_SESSION['flash_success'] = 'Candidato removido com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/candidates');
    }

    private function decidedBy(): ?int
    {
        /** @var mixed $userId */
        $userId = $_SESSION['user_id'] ?? null;

        return is_numeric($userId) ? (int) $userId : null;
    }
}
