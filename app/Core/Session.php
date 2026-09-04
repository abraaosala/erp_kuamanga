<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, mixed $value): void
    {
        /** @var array<string, mixed> $flash */
        $flash = $_SESSION['__flash'] ?? [];
        $flash[$key] = $value;
        $_SESSION['__flash'] = $flash;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        /** @var array<string, mixed> $flash */
        $flash = $_SESSION['__flash'] ?? [];
        $value = $flash[$key] ?? $default;
        unset($flash[$key]);
        $_SESSION['__flash'] = $flash;

        return $value;
    }

    public function hasFlash(string $key): bool
    {
        /** @var array<string, mixed> $flash */
        $flash = $_SESSION['__flash'] ?? [];

        return isset($flash[$key]);
    }

    public function old(string $key, mixed $value): void
    {
        /** @var array<string, mixed> $old */
        $old = $_SESSION['__old'] ?? [];
        $old[$key] = $value;
        $_SESSION['__old'] = $old;
    }

    public function getOld(string $key, mixed $default = null): mixed
    {
        /** @var array<string, mixed> $old */
        $old = $_SESSION['__old'] ?? [];

        return $old[$key] ?? $default;
    }

    public function hasOld(string $key): bool
    {
        /** @var array<string, mixed> $old */
        $old = $_SESSION['__old'] ?? [];

        return isset($old[$key]);
    }

    public function clearOld(): void
    {
        unset($_SESSION['__old']);
    }

    public function auth(mixed $user = null): mixed
    {
        if ($user !== null) {
            $this->set('user', $user);
            return $user;
        }
        return $this->get('user');
    }

    public function empresaId(?int $id = null): int
    {
        if ($id !== null) {
            $this->set('empresa_id', $id);
            return $id;
        }

        $value = $this->get('empresa_id', 1);

        return is_numeric($value) ? (int) $value : 1;
    }
}
