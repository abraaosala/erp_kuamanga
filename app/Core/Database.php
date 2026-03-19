<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;

class Database
{
    protected Container $container;
    protected Capsule $capsule;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function boot(): void
    {
        // Manual require to ensure classes are loaded if autoloader fails
        require_once BASE_PATH . '/vendor/illuminate/pagination/AbstractPaginator.php';
        require_once BASE_PATH . '/vendor/illuminate/pagination/Paginator.php';

        // Setup Paginator resolver
        Paginator::currentPageResolver(function ($pageName = 'page') {
            return (int) ($_GET[$pageName] ?? 1);
        });

        $config = config('database');

        $this->capsule = new Capsule();

        $default = $config['default'] ?? 'mysql';
        $connections = $config['connections'] ?? [];

        foreach ($connections as $name => $connection) {
            $this->capsule->addConnection($connection, $name);
        }

        // Ensure the 'default' connection is explicitly registered for Eloquent/Query Builder
        if (isset($connections[$default])) {
            $this->capsule->addConnection($connections[$default], 'default');
        }

        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        $this->container->instance('db', $this->capsule->getDatabaseManager());
        $this->container->instance('db.capsule', $this->capsule);
    }

    public function getCapsule(): Capsule
    {
        return $this->capsule;
    }
}
