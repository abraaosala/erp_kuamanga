<?php

declare(strict_types=1);

use App\Models\Candidate;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Interview;
use App\Models\JobOpening;
use App\Repositories\Modules\Rh\CandidateRepository;
use App\Repositories\Modules\Rh\EmployeeRepository;
use App\Repositories\Modules\Rh\InterviewRepository;
use App\Repositories\Modules\Rh\JobOpeningRepository;
use App\Repositories\Modules\Rh\PositionRepository;
use App\Services\Modules\Rh\CandidateService;
use App\Services\Modules\Rh\InterviewService;
use App\Services\Modules\Rh\JobOpeningService;

beforeEach(function (): void {
    $this->empresa = $this->createEmpresa();
    $_SESSION['empresa_id'] = $this->empresa->id;

    $this->jobOpeningRepo = new JobOpeningRepository();
    $this->candidateRepo = new CandidateRepository();
    $this->interviewRepo = new InterviewRepository();
    $this->employeeRepo = new EmployeeRepository();

    $this->jobOpeningService = new JobOpeningService($this->jobOpeningRepo, new PositionRepository());
    $this->candidateService = new CandidateService($this->candidateRepo, $this->jobOpeningRepo, $this->employeeRepo);
    $this->interviewService = new InterviewService($this->interviewRepo, $this->candidateRepo);

    $this->department = Department::create([
        'empresa_id' => $this->empresa->id,
        'name' => 'Operações',
        'status' => 'active',
    ]);

    $this->opening = $this->jobOpeningService->create([
        'title' => 'Assistente Administrativo',
        'department_id' => $this->department->id,
        'openings_count' => 2,
    ]);
});

it('creates a job opening with an open status by default', function (): void {
    expect($this->opening->status)->toBe(JobOpening::STATUS_ABERTA);
    expect($this->opening->openings_count)->toBe(2);
    expect(JobOpening::find($this->opening->id)->title)->toBe('Assistente Administrativo');
});

it('rejects an invalid opening status', function (): void {
    $this->jobOpeningService->create(['title' => 'Vaga Inválida', 'status' => 'ativa']);
})->throws(\InvalidArgumentException::class);

it('changes the status of a job opening', function (): void {
    $this->jobOpeningService->changeStatus((int) $this->opening->id, JobOpening::STATUS_PAUSADA);

    expect($this->jobOpeningService->getById((int) $this->opening->id)->status)->toBe(JobOpening::STATUS_PAUSADA);
});

it('registers a candidate attached to an open job opening', function (): void {
    $candidate = $this->candidateService->create([
        'name'           => 'Maria João',
        'email'          => 'maria@example.com',
        'job_opening_id' => $this->opening->id,
        'phone'          => '+244 923 000 000',
    ]);

    expect($candidate->status)->toBe(Candidate::STATUS_NOVO);
    expect($candidate->source)->toBe(Candidate::SOURCE_SITE);
    expect((int) $candidate->job_opening_id)->toBe((int) $this->opening->id);
});

it('normalizes email and forbids duplicates within the same empresa', function (): void {
    $this->candidateService->create([
        'name'  => 'Maria João',
        'email' => 'MARIA@Example.com',
    ]);

    expect(fn() => $this->candidateService->create([
        'name'  => 'Maria Sousa',
        'email' => 'maria@example.com',
    ]))->toThrow(\InvalidArgumentException::class);
});

it('allows the same email in a different empresa', function (): void {
    $this->candidateService->create(['name' => 'João', 'email' => 'joao@example.com']);

    $otherEmpresa = $this->createEmpresa();
    $_SESSION['empresa_id'] = $otherEmpresa->id;

    $candidate = $this->candidateService->create(['name' => 'João Outro', 'email' => 'joao@example.com']);

    expect((int) $candidate->empresa_id)->toBe((int) $otherEmpresa->id);
    expect(Candidate::where('email', 'joao@example.com')->count())->toBe(2);
});

it('forbids candidacy for a non-open job opening', function (): void {
    $this->jobOpeningService->changeStatus((int) $this->opening->id, JobOpening::STATUS_ENCERRADA);

    $this->candidateService->create([
        'name'           => 'Carlos',
        'email'          => 'carlos@example.com',
        'job_opening_id' => $this->opening->id,
    ]);
})->throws(\InvalidArgumentException::class);

it('moves a candidate through the pipeline stages', function (): void {
    $candidate = $this->candidateService->create([
        'name'  => 'Ana',
        'email' => 'ana@example.com',
    ]);

    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_TRIAGEM, 1);
    expect($candidate->status)->toBe(Candidate::STATUS_TRIAGEM);
    expect($candidate->decided_by)->toBe(1);
    expect($candidate->decided_at)->not->toBeNull();

    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_ENTREVISTA);
    expect($candidate->status)->toBe(Candidate::STATUS_ENTREVISTA);

    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_APROVADO);
    expect($candidate->status)->toBe(Candidate::STATUS_APROVADO);
});

it('prevents skipping pipeline stages', function (): void {
    $candidate = $this->candidateService->create(['name' => 'João', 'email' => 'joao.pipe@example.com']);

    $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_APROVADO);
})->throws(\InvalidArgumentException::class);

it('rejects a candidate from the pipeline', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Pedro', 'email' => 'pedro@example.com']);

    $result = $this->candidateService->reject((int) $candidate->id, 2);

    expect($result->status)->toBe(Candidate::STATUS_REJEITADO);
    expect($result->decided_by)->toBe(2);
});

it('does not allow changing a terminal status via pipeline', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Rui', 'email' => 'rui@example.com']);
    $this->candidateService->reject((int) $candidate->id);

    $this->candidateService->reject((int) $candidate->id);
})->throws(\InvalidArgumentException::class);

it('only hires approved candidates', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Luciana', 'email' => 'luciana@example.com']);

    $this->candidateService->hire((int) $candidate->id);
})->throws(\InvalidArgumentException::class);

it('hires an approved candidate and creates the employee record', function (): void {
    $candidate = $this->candidateService->create([
        'name'           => 'Luciana',
        'email'          => 'luciana.hire@example.com',
        'phone'          => '+244 900 000 000',
        'job_opening_id' => $this->opening->id,
    ]);

    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_TRIAGEM);
    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_ENTREVISTA);
    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_APROVADO);

    $hired = $this->candidateService->hire((int) $candidate->id, 1);

    expect($hired->status)->toBe(Candidate::STATUS_CONTRATADO);
    expect($hired->employee_id)->not->toBeNull();
    expect((int) $hired->employee_id)->toBeInt();

    $employee = Employee::find($hired->employee_id);
    expect($employee)->not->toBeNull();
    expect($employee->name)->toBe('Luciana');
    expect($employee->email)->toBe('luciana.hire@example.com');
    expect((int) $employee->department_id)->toBe((int) $this->department->id);
    expect($employee->status)->toBe('active');
});

it('retrieves the recruitment summary', function (): void {
    $this->candidateService->create(['name' => 'A', 'email' => 'a@example.com', 'job_opening_id' => $this->opening->id]);
    $this->candidateService->create(['name' => 'B', 'email' => 'b@example.com', 'job_opening_id' => $this->opening->id]);

    $summary = $this->jobOpeningService->summary();

    expect($summary['total'])->toBe(1);
    expect($summary['abertas'])->toBe(1);
    expect($summary['candidaturas'])->toBe(2);
    expect($summary['contratados'])->toBe(0);
});

it('schedules an interview for a candidate', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Inês', 'email' => 'ines@example.com']);

    $interview = $this->interviewService->create([
        'candidate_id' => (int) $candidate->id,
        'scheduled_at' => '2026-10-01 10:00:00',
        'interviewer'  => 'Dra. Carla',
    ]);

    expect($interview->result)->toBe(Interview::RESULT_PENDENTE);
    expect((int) $interview->job_opening_id)->toBe((int) $candidate->job_opening_id);
    expect($this->interviewService->byCandidate((int) $candidate->id))->toHaveCount(1);
});

it('forbids scheduling interviews for candidates in terminal status', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Marco', 'email' => 'marco@example.com']);
    $this->candidateService->reject((int) $candidate->id);

    $this->interviewService->create(['candidate_id' => (int) $candidate->id]);
})->throws(\InvalidArgumentException::class);

it('updates the result of an interview', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Sónia', 'email' => 'sonia@example.com']);
    $interview = $this->interviewService->create(['candidate_id' => (int) $candidate->id]);

    $this->interviewService->updateResult((int) $interview->id, Interview::RESULT_APROVADO);

    expect(Interview::find($interview->id)->result)->toBe(Interview::RESULT_APROVADO);
});

it('rejects an invalid interview result', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Paulo', 'email' => 'paulo@example.com']);
    $interview = $this->interviewService->create(['candidate_id' => (int) $candidate->id]);

    $this->interviewService->updateResult((int) $interview->id, 'desconhecido');
})->throws(\InvalidArgumentException::class);

it('counts candidates by status', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Mia', 'email' => 'mia@example.com']);
    $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_TRIAGEM);

    $counts = $this->candidateService->countByStatus();

    expect($counts[Candidate::STATUS_TRIAGEM])->toBe(1);
    expect($counts[Candidate::STATUS_NOVO])->toBe(0);
});

it('soft deletes a candidate', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Tomás', 'email' => 'tomas@example.com']);

    $this->candidateService->delete((int) $candidate->id);

    expect(Candidate::withTrashed()->find($candidate->id))->not->toBeNull();
    expect(Candidate::find($candidate->id))->toBeNull();
});

it('updates a candidate keeping the pipeline intact', function (): void {
    $candidate = $this->candidateService->create(['name' => 'Vera', 'email' => 'vera@example.com']);

    $this->candidateService->update((int) $candidate->id, ['phone' => '+244 911 000 000']);

    expect($this->candidateService->getById((int) $candidate->id)->phone)->toBe('+244 911 000 000');
    expect($this->candidateService->getById((int) $candidate->id)->status)->toBe(Candidate::STATUS_NOVO);
});
