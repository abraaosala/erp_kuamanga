<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\Foundation\MaintenanceMode;

class ApplicationContainer extends Container implements ApplicationContract
{
    protected bool $isBootstrapped = false;

    /** @var array<int, callable|string> */
    protected array $terminatingCallbacks = [];

    public function version(): string
    {
        return '1.0.0';
    }

    public function basePath($path = ''): string
    {
        return $this->joinPath($this->pathBase(), $this->pathString($path));
    }

    /** @return string */
    protected function pathBase(): string
    {
        /** @var string $base */
        $base = $this->make('path.base');

        return $base;
    }

    /** @return string */
    protected function pathStorage(): string
    {
        /** @var string $storage */
        $storage = $this->make('path.storage');

        return $storage;
    }

    /** @return string */
    protected function pathString(mixed $path): string
    {
        return is_string($path) ? $path : '';
    }

    public function bootstrapPath($path = ''): string
    {
        return $this->joinPath($this->basePath() . '/bootstrap', $this->pathString($path));
    }

    public function configPath($path = ''): string
    {
        return $this->joinPath($this->basePath() . '/config', $this->pathString($path));
    }

    public function databasePath($path = ''): string
    {
        return $this->joinPath($this->basePath() . '/database', $this->pathString($path));
    }

    public function langPath($path = ''): string
    {
        return $this->joinPath($this->basePath() . '/lang', $this->pathString($path));
    }

    public function publicPath($path = ''): string
    {
        return $this->joinPath($this->basePath() . '/public', $this->pathString($path));
    }

    public function resourcePath($path = ''): string
    {
        return $this->joinPath($this->basePath() . '/resources', $this->pathString($path));
    }

    public function storagePath($path = ''): string
    {
        return $this->joinPath($this->pathStorage(), $this->pathString($path));
    }

    /**
     * @param string|string[] ...$environments
     * @return string|bool
     */
    public function environment(...$environments)
    {
        if (count($environments) > 0) {
            $names = array_map(
                static fn(mixed $env): string => is_array($env) ? implode('|', $env) : (string) $env,
                $environments
            );

            return preg_match('/^' . implode('|', $names) . '$/', $this->environmentName()) === 1;
        }

        return $this->environmentName();
    }

    public function runningInConsole(): bool
    {
        return PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
    }

    public function runningUnitTests(): bool
    {
        return $this->environment('testing') === true;
    }

    public function hasDebugModeEnabled(): bool
    {
        /** @var array{debug: bool} $config */
        $config = $this->make('config.app');

        return $config['debug'];
    }

    public function maintenanceMode(): MaintenanceMode
    {
        return $this->make(MaintenanceMode::class);
    }

    public function isDownForMaintenance(): bool
    {
        return $this->maintenanceMode()->active();
    }

    public function registerConfiguredProviders(): void
    {
    }

    /**
     * @param string|\Illuminate\Support\ServiceProvider $provider
     */
    public function register($provider, $force = false)
    {
        return $this->resolveProvider($provider);
    }

    /**
     * @param string|\Illuminate\Support\ServiceProvider $provider
     */
    public function registerDeferredProvider($provider, $service = null): void
    {
        $this->resolveProvider($provider)->register();
    }

    /**
     * @param string|\Illuminate\Support\ServiceProvider $provider
     * @return \Illuminate\Support\ServiceProvider
     */
    public function resolveProvider($provider)
    {
        if ($provider instanceof \Illuminate\Support\ServiceProvider) {
            return $provider;
        }

        if (!class_exists($provider)) {
            throw new \InvalidArgumentException('Provider inválido.');
        }

        /** @var class-string<\Illuminate\Support\ServiceProvider> $class */
        $class = $provider;

        return new $class($this);
    }

    public function boot(): void
    {
        $this->isBootstrapped = true;
    }

    public function booting($callback): void
    {
    }

    public function booted($callback): void
    {
        $this->terminatingCallbacks[] = $callback;
    }

    /**
     * @param array<int, mixed> $bootstrappers
     */
    public function bootstrapWith(array $bootstrappers): void
    {
        $this->isBootstrapped = true;
    }

    public function getLocale(): string
    {
        /** @var array{locale: string} $config */
        $config = $this->make('config.app');

        return $config['locale'];
    }

    public function getNamespace(): string
    {
        return 'App\\';
    }

    /**
     * @return array<int, mixed>
     */
    public function getProviders($provider): array
    {
        return [];
    }

    public function hasBeenBootstrapped(): bool
    {
        return $this->isBootstrapped;
    }

    public function loadDeferredProviders(): void
    {
    }

    public function setLocale($locale): void
    {
        $this->instance('config.locale', $locale);
    }

    public function shouldSkipMiddleware(): bool
    {
        return $this->runningUnitTests();
    }

    public function terminating($callback)
    {
        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    public function terminate(): void
    {
        foreach ($this->terminatingCallbacks as $callback) {
            $this->call($callback);
        }
    }

    protected function environmentName(): string
    {
        /** @var array{env: string} $config */
        $config = $this->make('config.app');

        return $config['env'];
    }

    protected function joinPath(string $base, string $path): string
    {
        return $path === ''
            ? $base
            : rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim((string) $path, DIRECTORY_SEPARATOR);
    }
}
