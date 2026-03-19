<?php

declare(strict_types=1);

namespace App\Providers\Modules\User;

use App\Http\Controllers\Modules\User\AuthController;
use App\Http\Controllers\Modules\User\UserController;
use App\Http\Middleware\AuthMiddleware;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Modules\User\RoleRepository;
use App\Repositories\Modules\User\UserRepository;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Modules\User\UserService;
use Illuminate\Container\Container;

class UserServiceProvider
{
    public function __construct(protected Container $container) {}

    public function register(): void
    {
        // Bind RoleRepository
        $this->container->bind(RoleRepositoryInterface::class, RoleRepository::class);

        // Bind UserService
        $this->container->bind(UserServiceInterface::class, UserService::class);
    }

    public function boot(): void
    {
        // Register routes for Auth and Users via external files
        $router = $this->container->make('router');
        $container = $this->container;

        require BASE_PATH . '/routes/auth.php';
        require BASE_PATH . '/routes/users.php';
    }
}
