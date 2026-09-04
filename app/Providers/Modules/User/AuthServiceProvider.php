<?php

declare(strict_types=1);

namespace App\Providers\Modules\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Modules\User\UserRepository;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Modules\User\AuthService;
use Illuminate\Container\Container;
use eftec\bladeone\BladeOne;

class AuthServiceProvider
{
    public function __construct(protected Container $container) {}

    public function register(): void
    {
        // Bind UserRepository for AuthService dependency
        $this->container->bind(UserRepositoryInterface::class, UserRepository::class);

        // Bind AuthService
        $this->container->bind(AuthServiceInterface::class, AuthService::class);

        // Register BladeOne
        $this->container->singleton(BladeOne::class, function () {
            /** @var string $basePath */
            $basePath = $this->container->make('path.base');
            $views = $basePath . '/resources/views';
            $cache = $basePath . '/storage/cache';

            if (!is_dir($cache)) {
                mkdir($cache, 0755, true);
            }

            return new BladeOne($views, $cache, BladeOne::MODE_AUTO);
        });

        // Validator is registered globally in Application::bootstrapValidation()
        // with presence verifier support. Do not override it here.
    }

    public function boot(): void
    {
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->container->make('router');
        $router->aliasMiddleware('auth', \App\Http\Middleware\AuthMiddleware::class);
    }
}
