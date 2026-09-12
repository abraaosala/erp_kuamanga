<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Rh;

use App\Models\Interview;
use App\Services\Contracts\InterviewServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class InterviewController
{
    public function __construct(
        protected InterviewServiceInterface $interviewService,
        protected Validator $validator,
    ) {}

    public function store(Request $request, int $candidateId): RedirectResponse
    {
        $data = $request->all();
        $data['candidate_id'] = $candidateId;

        $validation = $this->validator->make($data, [
            'candidate_id'  => 'required|integer',
            'scheduled_at'  => 'nullable|date',
            'interviewer'   => 'nullable|max:150',
            'result'        => 'nullable|in:' . implode(',', Interview::RESULTS),
            'notes'         => 'nullable|string',
        ], [
            'candidate_id.required' => 'O candidato é obrigatório.',
            'result.in'             => 'Resultado da entrevista inválido.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/rh/candidates/' . $candidateId);
        }

        try {
            $this->interviewService->create($data);
            $_SESSION['flash_success'] = 'Entrevista agendada!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/candidates/' . $candidateId);
    }

    public function result(Request $request, int $candidateId, int $interviewId): RedirectResponse
    {
        $resultRaw = $request->get('result');
        /** @var string|null $result */
        $result = is_string($resultRaw) ? $resultRaw : null;

        try {
            if (!$result) {
                throw new \InvalidArgumentException('Resultado da entrevista inválido.');
            }
            $this->interviewService->updateResult($interviewId, $result);
            $_SESSION['flash_success'] = 'Resultado da entrevista atualizado!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/candidates/' . $candidateId);
    }

    public function destroy(Request $request, int $candidateId, int $interviewId): RedirectResponse
    {
        try {
            $this->interviewService->delete($interviewId);
            $_SESSION['flash_success'] = 'Entrevista removida.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/rh/candidates/' . $candidateId);
    }
}
