<?php

declare(strict_types=1);

use App\Http\Controllers\Modules\User\UserController;
use App\Http\Middleware\AuthMiddleware;
use App\Services\Contracts\AuthServiceInterface;
use eftec\bladeone\BladeOne;

/** @var \Illuminate\Routing\Router $router */
/** @var \Illuminate\Container\Container $container */

// Protected routes (require authentication)
$router->group(['middleware' => [AuthMiddleware::class]], function() use ($router, $container) {
    
    // Dashboard
    $router->get('/dashboard', function () use ($container) {
        $blade   = $container->make(BladeOne::class);
        $user    = $container->make(AuthServiceInterface::class)->user();
        $session = $container->make(\App\Core\Session::class);
        $html    = $blade->run('dashboard', [
            'user'    => $user,
            'success' => $session->getFlash('success'),
        ]);
        return response($html);
    });

    // Users CRUD
    $router->get('/users', function () use ($container) {
        $request = $container->make('request');
        return $container->make(UserController::class)->index($request);
    });

    $router->get('/users/create', function () use ($container) {
        $request = $container->make('request');
        return $container->make(UserController::class)->create($request);
    });

    $router->post('/users', function () use ($container) {
        $request = $container->make('request');
        return $container->make(UserController::class)->store($request);
    });

    $router->get('/users/{id}/edit', function (int $id) use ($container) {
        $request = $container->make('request');
        return $container->make(UserController::class)->edit($request, $id);
    });

    $router->post('/users/{id}/update', function (int $id) use ($container) {
        $request = $container->make('request');
        return $container->make(UserController::class)->update($request, $id);
    });

    $router->post('/users/{id}/delete', function (int $id) use ($container) {
        $request = $container->make('request');
        return $container->make(UserController::class)->destroy($request, $id);
    });
});
