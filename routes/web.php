<?php

declare(strict_types=1);

use Illuminate\Routing\Router;

/** @var Router $router */

// Root redirect
$router->get('/', function () {
    return redirect('/dashboard');
});

$router->get('/companies', [\App\Http\Controllers\CompanyController::class, 'index'])->middleware('auth');
$router->post('/company/switch', [\App\Http\Controllers\CompanyController::class, 'switch'])->middleware('auth');
