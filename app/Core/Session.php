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

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
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

    public function flash(string $key, $value): void
    {
        $_SESSION['__flash'][$key] = $value;
    }

    public function getFlash(string $key, $default = null)
    {
        $value = $_SESSION['__flash'][$key] ?? $default;
        unset($_SESSION['__flash'][$key]);
        return $value;
    }

    public function hasFlash(string $key): bool
    {
        return isset($_SESSION['__flash'][$key]);
    }

    public function old(string $key, $value): void
    {
        $_SESSION['__old'][$key] = $value;
    }

    public function getOld(string $key, $default = null)
    {
        return $_SESSION['__old'][$key] ?? $default;
    }

    public function hasOld(string $key): bool
    {
        return isset($_SESSION['__old'][$key]);
    }

    public function clearOld(): void
    {
        unset($_SESSION['__old']);
    }

    public function auth($user = null)
    {
        if ($user !== null) {
            $this->set('user', $user);
            return $user;
        }
        return $this->get('user');
    }

    public function empresaId(int $id = null)
    {
        if ($id !== null) {
            $this->set('empresa_id', $id);
            return $id;
        }
        return (int)$this->get('empresa_id', 1);
    }
}
