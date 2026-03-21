<?php

declare(strict_types=1);

namespace App\Providers\Modules\Accounting;

use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Modules\Accounting\AccountRepository;
use App\Services\Contracts\AccountServiceInterface;
use App\Services\Modules\Accounting\AccountService;
use Illuminate\Container\Container;

class AccountingServiceProvider
{
    public function __construct(protected Container $container) {}

    public function register(): void
    {
        // Bind Repository
        $this->container->bind(AccountRepositoryInterface::class, AccountRepository::class);

        // Bind Service
        $this->container->bind(AccountServiceInterface::class, AccountService::class);
    }

    public function boot(): void
    {
        // Load accounting routes
        $router = $this->container->make('router');
        require BASE_PATH . '/routes/accounting.php';
    }
}
