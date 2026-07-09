<?php

declare(strict_types=1);

use App\Http\Controllers\Modules\Rh\AttendanceController;
use App\Http\Controllers\Modules\Rh\ContractController;
use App\Http\Controllers\Modules\Rh\DepartmentController;
use App\Http\Controllers\Modules\Rh\EmployeeController;
use App\Http\Controllers\Modules\Rh\PositionController;
use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'rh', 'middleware' => 'auth'], function (Router $router) {
    $router->get('/attendance', [AttendanceController::class, 'index'])->name('rh.attendance.index');
    $router->get('/attendance/create', [AttendanceController::class, 'create'])->name('rh.attendance.create');
    $router->post('/attendance', [AttendanceController::class, 'store'])->name('rh.attendance.store');
    $router->get('/attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('rh.attendance.edit');
    $router->post('/attendance/{id}/update', [AttendanceController::class, 'update'])->name('rh.attendance.update');
    $router->post('/attendance/{id}/delete', [AttendanceController::class, 'destroy'])->name('rh.attendance.destroy');

    $router->get('/employees', [EmployeeController::class, 'index'])->name('rh.employees.index');
    $router->get('/employees/create', [EmployeeController::class, 'create'])->name('rh.employees.create');
    $router->post('/employees', [EmployeeController::class, 'store'])->name('rh.employees.store');
    $router->get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('rh.employees.edit');
    $router->post('/employees/{id}/update', [EmployeeController::class, 'update'])->name('rh.employees.update');
    $router->post('/employees/{id}/delete', [EmployeeController::class, 'destroy'])->name('rh.employees.destroy');

    $router->get('/departments', [DepartmentController::class, 'index'])->name('rh.departments.index');
    $router->get('/departments/create', [DepartmentController::class, 'create'])->name('rh.departments.create');
    $router->post('/departments', [DepartmentController::class, 'store'])->name('rh.departments.store');
    $router->get('/departments/{id}/edit', [DepartmentController::class, 'edit'])->name('rh.departments.edit');
    $router->post('/departments/{id}/update', [DepartmentController::class, 'update'])->name('rh.departments.update');
    $router->post('/departments/{id}/delete', [DepartmentController::class, 'destroy'])->name('rh.departments.destroy');

    $router->get('/positions', [PositionController::class, 'index'])->name('rh.positions.index');
    $router->get('/positions/create', [PositionController::class, 'create'])->name('rh.positions.create');
    $router->post('/positions', [PositionController::class, 'store'])->name('rh.positions.store');
    $router->get('/positions/{id}/edit', [PositionController::class, 'edit'])->name('rh.positions.edit');
    $router->post('/positions/{id}/update', [PositionController::class, 'update'])->name('rh.positions.update');
    $router->post('/positions/{id}/delete', [PositionController::class, 'destroy'])->name('rh.positions.destroy');

    $router->get('/contracts', [ContractController::class, 'index'])->name('rh.contracts.index');
    $router->get('/contracts/create', [ContractController::class, 'create'])->name('rh.contracts.create');
    $router->post('/contracts', [ContractController::class, 'store'])->name('rh.contracts.store');
    $router->get('/contracts/{id}/edit', [ContractController::class, 'edit'])->name('rh.contracts.edit');
    $router->post('/contracts/{id}/update', [ContractController::class, 'update'])->name('rh.contracts.update');
    $router->post('/contracts/{id}/delete', [ContractController::class, 'destroy'])->name('rh.contracts.destroy');
});
