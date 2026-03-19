<?php

declare(strict_types=1);

use Illuminate\Routing\Router;

/** @var Router $router */

// Root redirect
$router->get('/', function () {
    return redirect('/dashboard');
});
