<?php

declare(strict_types=1);

use App\Models\Candidate;
use App\Models\Department;
use App\Models\Position;
use App\Repositories\Modules\Rh\BenefitRepository;
use App\Repositories\Modules\Rh\CandidateRepository;
use App\Repositories\Modules\Rh\EmployeeRepository;
use App\Repositories\Modules\Rh\JobOpeningRepository;
use App\Repositories\Modules\Rh\PositionRepository;
use App\Services\Modules\Rh\BenefitService;
use App\Services\Modules\Rh\CandidateService;
use App\Services\Modules\Rh\EmployeeService;
use App\Services\Modules\Rh\JobOpeningService;

beforeEach(function (): void {
    $this->empresa = $this->createEmpresa();
    $_SESSION['empresa_id'] = $this->empresa->id;

    $this->employeeService = new EmployeeService(new EmployeeRepository(), new PositionRepository());
    $this->benefitService = new BenefitService(new BenefitRepository(), new EmployeeRepository(), new PositionRepository());
    $this->jobOpeningService = new JobOpeningService(new JobOpeningRepository(), new PositionRepository());
    $this->candidateService = new CandidateService(new CandidateRepository(), new JobOpeningRepository(), new EmployeeRepository());

    $this->deptA = Department::create([
        'empresa_id' => $this->empresa->id,
        'name' => 'Operações',
        'status' => 'active',
    ]);

    $this->deptB = Department::create([
        'empresa_id' => $this->empresa->id,
        'name' => 'Vendas',
        'status' => 'active',
    ]);

    $this->positionA = Position::create([
        'empresa_id' => $this->empresa->id,
        'department_id' => $this->deptA->id,
        'name' => 'Operador',
        'status' => 'active',
    ]);

    $this->positionB = Position::create([
        'empresa_id' => $this->empresa->id,
        'department_id' => $this->deptB->id,
        'name' => 'Vendedor',
        'status' => 'active',
    ]);
});

it('derives the employee department from the chosen position', function (): void {
    $employee = $this->employeeService->create([
        'name' => 'Derivada',
        'position_id' => $this->positionA->id,
    ]);

    expect((int) $employee->department_id)->toBe((int) $this->deptA->id);
});

it('keeps the employee department when it matches the position', function (): void {
    $employee = $this->employeeService->create([
        'name' => 'Coerente',
        'position_id' => $this->positionA->id,
        'department_id' => $this->deptA->id,
    ]);

    expect((int) $employee->department_id)->toBe((int) $this->deptA->id);
});

it('rejects an employee whose department contradicts the position', function (): void {
    expect(fn() => $this->employeeService->create([
        'name' => 'Contradição',
        'position_id' => $this->positionA->id,
        'department_id' => $this->deptB->id,
    ]))->toThrow(\InvalidArgumentException::class, 'outro departamento');
});

it('re-derives the employee department when only the position changes', function (): void {
    $employee = $this->employeeService->create([
        'name' => 'Muda de Cargo',
        'position_id' => $this->positionA->id,
    ]);

    $this->employeeService->update((int) $employee->id, [
        'position_id' => $this->positionB->id,
    ]);

    $refresh = $this->employeeService->getById((int) $employee->id);
    expect((int) $refresh->department_id)->toBe((int) $this->deptB->id);
});

it('rejects an employee update that keeps a contradictory department', function (): void {
    $employee = $this->employeeService->create([
        'name' => 'Inconsistente',
        'position_id' => $this->positionA->id,
        'department_id' => $this->deptA->id,
    ]);

    expect(fn() => $this->employeeService->update((int) $employee->id, [
        'department_id' => $this->deptB->id,
    ]))->toThrow(\InvalidArgumentException::class);
});

it('derives the job opening department from the chosen position', function (): void {
    $opening = $this->jobOpeningService->create([
        'title' => 'Vaga de Operador',
        'position_id' => $this->positionA->id,
    ]);

    expect((int) $opening->department_id)->toBe((int) $this->deptA->id);
});

it('rejects a job opening whose department contradicts the position', function (): void {
    expect(fn() => $this->jobOpeningService->create([
        'title' => 'Vaga Contraditória',
        'position_id' => $this->positionA->id,
        'department_id' => $this->deptB->id,
    ]))->toThrow(\InvalidArgumentException::class, 'outro departamento');
});

it('re-derives the job opening department when the position changes', function (): void {
    $opening = $this->jobOpeningService->create([
        'title' => 'Vaga em Mudança',
        'position_id' => $this->positionA->id,
    ]);

    $this->jobOpeningService->update((int) $opening->id, [
        'position_id' => $this->positionB->id,
    ]);

    $refresh = $this->jobOpeningService->getById((int) $opening->id);
    expect((int) $refresh->department_id)->toBe((int) $this->deptB->id);
});

it('accepts a benefit whose position matches its department', function (): void {
    $benefit = $this->benefitService->create([
        'name' => 'Seguro Cargo+Dept',
        'position_id' => $this->positionA->id,
        'department_id' => $this->deptA->id,
    ]);

    expect((int) $benefit->department_id)->toBe((int) $this->deptA->id);
});

it('rejects a benefit whose position contradicts its department', function (): void {
    expect(fn() => $this->benefitService->create([
        'name' => 'Seguro Impossível',
        'position_id' => $this->positionA->id,
        'department_id' => $this->deptB->id,
    ]))->toThrow(\InvalidArgumentException::class, 'outro departamento');
});

it('rejects a benefit update that adds a position from another department', function (): void {
    $benefit = $this->benefitService->create([
        'name' => 'Seguro Só Depto',
        'department_id' => $this->deptA->id,
    ]);

    expect(fn() => $this->benefitService->update((int) $benefit->id, [
        'position_id' => $this->positionB->id,
    ]))->toThrow(\InvalidArgumentException::class);
});

it('keeps hiring coherent when the opening derives its department', function (): void {
    $opening = $this->jobOpeningService->create([
        'title' => 'Vaga de Vendedor',
        'position_id' => $this->positionB->id,
    ]);

    $candidate = $this->candidateService->create([
        'name' => 'Inês',
        'email' => 'ines.rule@example.com',
        'job_opening_id' => $opening->id,
    ]);

    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_TRIAGEM);
    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_ENTREVISTA);
    $candidate = $this->candidateService->transition((int) $candidate->id, Candidate::STATUS_APROVADO);

    $hired = $this->candidateService->hire((int) $candidate->id);

    expect((int) $hired->employee_id)->toBeGreaterThan(0);
    $employee = $this->employeeService->getById((int) $hired->employee_id);
    expect((int) $employee->department_id)->toBe((int) $this->deptB->id);
});
