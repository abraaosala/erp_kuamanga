<?php

declare(strict_types=1);

use App\Http\Controllers\Modules\User\AuthController;

/** @var \Illuminate\Routing\Router $router */
/** @var \Illuminate\Container\Container $container */

// Auth routes (public)
$router->get('/login',    function () use ($container) {
    $request = $container->make('request');
    return $container->make(AuthController::class)->showLogin($request);
});

$router->post('/login',   function () use ($container) {
    $request = $container->make('request');
    return $container->make(AuthController::class)->login($request);
});

$router->post('/logout',  function () use ($container) {
    $request = $container->make('request');
    return $container->make(AuthController::class)->logout($request);
});
