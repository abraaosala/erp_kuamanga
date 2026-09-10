<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__));
        }

        require_once BASE_PATH . '/vendor/autoload.php';
        require_once BASE_PATH . '/app/Core/helpers.php';

        $dbPath = BASE_PATH . '/database/test.sqlite';
        file_put_contents($dbPath, '');

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => $dbPath,
            'prefix'   => '',
        ], 'default');
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        Container::setInstance($capsule->getContainer());
        $capsule->getContainer()->instance('db', $capsule->getDatabaseManager());

        \Illuminate\Support\Facades\Facade::setFacadeApplication($capsule->getContainer());

        $this->runMigrations();
    }

    protected function tearDown(): void
    {
        $dbPath = BASE_PATH . '/database/test.sqlite';

        try {
            $db = app('db');
            if ($db && method_exists($db, 'purge')) {
                $db->purge('default');
            }
            if ($db && method_exists($db, 'disconnect')) {
                $db->disconnect('default');
            }
        } catch (\Throwable) {
            // ignore
        }

        if (file_exists($dbPath)) {
            @file_put_contents($dbPath, '');
        }
    }

    protected function runMigrations(): void
    {
        $schema = Capsule::schema('default');

        $schema->create('empresas', function ($table) {
            $table->id();
            $table->string('nome');
            $table->string('nif')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('departments', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('positions', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->decimal('salary_range_min', 12, 2)->nullable();
            $table->decimal('salary_range_max', 12, 2)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('employees', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->string('name', 150);
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->unsignedInteger('position_id')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('contracts', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->unsignedInteger('employee_id');
            $table->string('tipo_contrato', 50);
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->decimal('salario_base', 12, 2)->nullable();
            $table->string('carga_horaria', 50)->nullable();
            $table->text('observacoes')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('attendance', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->unsignedInteger('employee_id');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status', 20)->default('presente');
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('hour_bank_entries', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->unsignedInteger('employee_id');
            $table->date('date');
            $table->decimal('hours', 8, 2);
            $table->string('type', 30)->default('ajuste');
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('work_schedules', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->string('name', 100);
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('break_minutes')->default(0);
            $table->string('days_of_week', 50)->nullable();
            $table->string('status', 20)->default('ativo');
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('employee_schedules', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('work_schedule_id');
            $table->boolean('is_default')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'work_schedule_id']);
        });

        $schema->create('payroll_runs', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('description')->nullable();
            $table->string('status', 20)->default('rascunho');
            $table->decimal('total_gross', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('total_net', 14, 2)->default(0);
            $table->unsignedInteger('employee_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $schema->create('payslips', function ($table) {
            $table->id();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->unsignedInteger('payroll_run_id');
            $table->unsignedInteger('employee_id');
            $table->decimal('gross_salary', 14, 2)->default(0);
            $table->decimal('base_salary', 14, 2)->default(0);
            $table->decimal('overtime_amount', 14, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('absent_days', 8, 2)->default(0);
            $table->decimal('absent_deduction', 14, 2)->default(0);
            $table->decimal('net_salary', 14, 2)->default(0);
            $table->string('status', 20)->default('rascunho');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function createEmpresa(): \App\Models\Empresa
    {
        return \App\Models\Empresa::create(['nome' => 'Empresa Teste']);
    }

    protected function createEmployee(int $empresaId): \App\Models\Employee
    {
        return \App\Models\Employee::create([
            'empresa_id' => $empresaId,
            'name' => 'Funcionário Teste',
            'email' => 'teste@example.com',
        ]);
    }

    protected function createSchedule(int $empresaId): \App\Models\WorkSchedule
    {
        return \App\Models\WorkSchedule::create([
            'empresa_id' => $empresaId,
            'name' => 'Turno Normal',
            'check_in_time' => '08:00',
            'check_out_time' => '17:00',
            'break_minutes' => 60,
            'days_of_week' => '1,2,3,4,5',
            'status' => 'ativo',
        ]);
    }
}
