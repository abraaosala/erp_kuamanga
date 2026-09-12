<?php

declare(strict_types=1);

namespace App\Providers\Modules\Rh;

use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\BenefitRepositoryInterface;
use App\Repositories\Contracts\CandidateRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Repositories\Contracts\EmployeeDocumentRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\EmployeeScheduleRepositoryInterface;
use App\Repositories\Contracts\HourBankEntryRepositoryInterface;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use App\Repositories\Contracts\JobOpeningRepositoryInterface;
use App\Repositories\Contracts\LeaveRepositoryInterface;
use App\Repositories\Contracts\PayrollRepositoryInterface;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Repositories\Contracts\RosterRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use App\Repositories\Modules\Rh\AttendanceRepository;
use App\Repositories\Modules\Rh\BenefitRepository;
use App\Repositories\Modules\Rh\CandidateRepository;
use App\Repositories\Modules\Rh\ContractRepository;
use App\Repositories\Modules\Rh\DepartmentRepository;
use App\Repositories\Modules\Rh\EmployeeDocumentRepository;
use App\Repositories\Modules\Rh\EmployeeRepository;
use App\Repositories\Modules\Rh\EmployeeScheduleRepository;
use App\Repositories\Modules\Rh\HourBankEntryRepository;
use App\Repositories\Modules\Rh\InterviewRepository;
use App\Repositories\Modules\Rh\JobOpeningRepository;
use App\Repositories\Modules\Rh\LeaveRepository;
use App\Repositories\Modules\Rh\PayrollRepository;
use App\Repositories\Modules\Rh\PositionRepository;
use App\Repositories\Modules\Rh\RosterRepository;
use App\Repositories\Modules\Rh\WorkScheduleRepository;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\BenefitServiceInterface;
use App\Services\Contracts\CandidateServiceInterface;
use App\Services\Contracts\ContractServiceInterface;
use App\Services\Contracts\DepartmentServiceInterface;
use App\Services\Contracts\EmployeeDocumentServiceInterface;
use App\Services\Contracts\EmployeeScheduleServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\HourBankEntryServiceInterface;
use App\Services\Contracts\InterviewServiceInterface;
use App\Services\Contracts\JobOpeningServiceInterface;
use App\Services\Contracts\LeaveServiceInterface;
use App\Services\Contracts\PayrollServiceInterface;
use App\Services\Contracts\PositionServiceInterface;
use App\Services\Contracts\RosterServiceInterface;
use App\Services\Contracts\WorkScheduleServiceInterface;
use App\Services\Modules\Rh\AttendanceService;
use App\Services\Modules\Rh\BenefitService;
use App\Services\Modules\Rh\CandidateService;
use App\Services\Modules\Rh\ContractService;
use App\Services\Modules\Rh\DepartmentService;
use App\Services\Modules\Rh\EmployeeDocumentService;
use App\Services\Modules\Rh\EmployeeScheduleService;
use App\Services\Modules\Rh\EmployeeService;
use App\Services\Modules\Rh\HourBankEntryService;
use App\Services\Modules\Rh\InterviewService;
use App\Services\Modules\Rh\JobOpeningService;
use App\Services\Modules\Rh\LeaveService;
use App\Services\Modules\Rh\PayrollService;
use App\Services\Modules\Rh\PositionService;
use App\Services\Modules\Rh\RosterService;
use App\Services\Modules\Rh\WorkScheduleService;
use Illuminate\Container\Container;

class RhServiceProvider
{
    public function __construct(protected Container $container) {}

    public function register(): void
    {
        $this->container->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->container->bind(AttendanceServiceInterface::class, AttendanceService::class);
        $this->container->bind(BenefitRepositoryInterface::class, BenefitRepository::class);
        $this->container->bind(BenefitServiceInterface::class, BenefitService::class);
        $this->container->bind(ContractRepositoryInterface::class, ContractRepository::class);
        $this->container->bind(ContractServiceInterface::class, ContractService::class);
        $this->container->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->container->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->container->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->container->bind(DepartmentServiceInterface::class, DepartmentService::class);
        $this->container->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->container->bind(PositionServiceInterface::class, PositionService::class);
        $this->container->bind(WorkScheduleRepositoryInterface::class, WorkScheduleRepository::class);
        $this->container->bind(WorkScheduleServiceInterface::class, WorkScheduleService::class);
        $this->container->bind(HourBankEntryRepositoryInterface::class, HourBankEntryRepository::class);
        $this->container->bind(HourBankEntryServiceInterface::class, HourBankEntryService::class);
        $this->container->bind(LeaveRepositoryInterface::class, LeaveRepository::class);
        $this->container->bind(LeaveServiceInterface::class, LeaveService::class);
        $this->container->bind(EmployeeScheduleRepositoryInterface::class, EmployeeScheduleRepository::class);
        $this->container->bind(EmployeeScheduleServiceInterface::class, EmployeeScheduleService::class);
        $this->container->bind(EmployeeDocumentRepositoryInterface::class, EmployeeDocumentRepository::class);
        $this->container->bind(EmployeeDocumentServiceInterface::class, EmployeeDocumentService::class);
        $this->container->bind(PayrollRepositoryInterface::class, PayrollRepository::class);
        $this->container->bind(PayrollServiceInterface::class, PayrollService::class);
        $this->container->bind(RosterRepositoryInterface::class, RosterRepository::class);
        $this->container->bind(RosterServiceInterface::class, RosterService::class);
        $this->container->bind(JobOpeningRepositoryInterface::class, JobOpeningRepository::class);
        $this->container->bind(CandidateRepositoryInterface::class, CandidateRepository::class);
        $this->container->bind(InterviewRepositoryInterface::class, InterviewRepository::class);
        $this->container->bind(JobOpeningServiceInterface::class, JobOpeningService::class);
        $this->container->bind(CandidateServiceInterface::class, CandidateService::class);
        $this->container->bind(InterviewServiceInterface::class, InterviewService::class);
    }

    public function boot(): void
    {
        $router = $this->container->make('router');
        require BASE_PATH . '/routes/rh.php';
    }
}
