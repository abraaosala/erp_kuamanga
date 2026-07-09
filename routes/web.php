<?php

declare(strict_types=1);

use Illuminate\Routing\Router;

/** @var Router $router */

// Root redirect
$router->get('/', function () {
    return redirect('/dashboard');
});




$router->get('/companies', [\App\Http\Controllers\CompanyController::class, 'index'])->middleware('auth');
$router->get('/companies/create', [\App\Http\Controllers\CompanyController::class, 'create'])->middleware('auth');
$router->post('/companies', [\App\Http\Controllers\CompanyController::class, 'store'])->middleware('auth');
$router->get('/companies/{id}/edit', [\App\Http\Controllers\CompanyController::class, 'edit'])->middleware('auth');
$router->post('/companies/{id}/update', [\App\Http\Controllers\CompanyController::class, 'update'])->middleware('auth');
$router->post('/companies/{id}/delete', [\App\Http\Controllers\CompanyController::class, 'destroy'])->middleware('auth');
$router->post('/company/switch', [\App\Http\Controllers\CompanyController::class, 'switch'])->middleware('auth');
