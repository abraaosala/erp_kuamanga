<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Support\Facades\Facade;

class Application
{
    protected Container $container;
    protected Router $router;
    protected array $providers = [];

    public function __construct(string $basePath)
    {
        $this->container = new Container();
        Container::setInstance($this->container);

        $this->container->instance('app', $this->container);
        $this->container->instance('path.base', $basePath);
        $this->container->instance('path.config', $basePath . '/config');
        $this->container->instance('path.storage', $basePath . '/storage');

        Facade::setFacadeApplication($this->container);

        $this->bootstrapConfig();
        $this->bootstrapSession();
        $this->bootstrapRouting();
        $this->bootstrapDatabase();
        $this->bootstrapErrorHandling();
        $this->registerProviders();
    }

    protected function bootstrapConfig(): void
    {
        $configPath = $this->container->make('path.config');
        $files = glob($configPath . '/*.php');

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $this->container->instance('config.' . $name, require $file);
        }

        $appConfig = $this->container->make('config.app');
        date_default_timezone_set($appConfig['timezone'] ?? 'UTC');
    }

    protected function bootstrapSession(): void
    {
        $appConfig = $this->container->make('config.app');
        $sessionPath = $appConfig['session']['path'] ?? sys_get_temp_dir();

        if (!is_dir($sessionPath)) {
            mkdir($sessionPath, 0755, true);
        }

        session_save_path($sessionPath);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function bootstrapRouting(): void
    {
        (new EventServiceProvider($this->container))->register();
        (new RoutingServiceProvider($this->container))->register();

        $this->router = $this->container->make('router');
        $this->container->instance('router', $this->router);
    }

    protected function bootstrapDatabase(): void
    {
        $database = new Database($this->container);
        $database->boot();
    }

    protected function bootstrapErrorHandling(): void
    {
        $appConfig = $this->container->make('config.app');
        if (filter_var($appConfig['debug'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $whoops = new \Whoops\Run();
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            $whoops->register();
        }
    }

    protected function registerProviders(): void
    {
        $appConfig = $this->container->make('config.app');
        $providers = $appConfig['providers'] ?? [];

        foreach ($providers as $providerClass) {
            if (class_exists($providerClass)) {
                $provider = new $providerClass($this->container);
                $provider->register();
                $this->providers[] = $provider;
            }
        }

        foreach ($this->providers as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
    }

    public function loadRoutes(): void
    {
        $router = $this->router;
        require $this->container->make('path.base') . '/routes/web.php';
    }

    public function run(): void
    {
        $this->loadRoutes();

        $request = Request::capture();
        $this->container->instance('request', $request);

        try {
            $response = $this->router->dispatch($request);
            $response->send();
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            http_response_code(404);
            echo '<h1>404 - Página não encontrada</h1>';
        } catch (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            http_response_code(405);
            echo '<h1>405 - Método não permitido</h1>';
        } catch (\Exception $e) {
            if ($this->container->make('config.app')['debug'] ?? false) {
                throw $e;
            }
            http_response_code(500);
            echo '<h1>500 - Erro interno do servidor</h1>';
        }
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
