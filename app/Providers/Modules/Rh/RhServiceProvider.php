<?php

declare(strict_types=1);

namespace App\Providers\Modules\Rh;

use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Repositories\Modules\Rh\AttendanceRepository;
use App\Repositories\Modules\Rh\ContractRepository;
use App\Repositories\Modules\Rh\DepartmentRepository;
use App\Repositories\Modules\Rh\EmployeeRepository;
use App\Repositories\Modules\Rh\PositionRepository;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\ContractServiceInterface;
use App\Services\Contracts\DepartmentServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\PositionServiceInterface;
use App\Services\Modules\Rh\AttendanceService;
use App\Services\Modules\Rh\ContractService;
use App\Services\Modules\Rh\DepartmentService;
use App\Services\Modules\Rh\EmployeeService;
use App\Services\Modules\Rh\PositionService;
use Illuminate\Container\Container;

class RhServiceProvider
{
    public function __construct(protected Container $container) {}

    public function register(): void
    {
        $this->container->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->container->bind(AttendanceServiceInterface::class, AttendanceService::class);
        $this->container->bind(ContractRepositoryInterface::class, ContractRepository::class);
        $this->container->bind(ContractServiceInterface::class, ContractService::class);
        $this->container->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->container->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->container->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->container->bind(DepartmentServiceInterface::class, DepartmentService::class);
        $this->container->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->container->bind(PositionServiceInterface::class, PositionService::class);
    }

    public function boot(): void
    {
        $router = $this->container->make('router');
        require BASE_PATH . '/routes/rh.php';
    }
}
