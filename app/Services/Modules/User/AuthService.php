<?php

declare(strict_types=1);

namespace App\Services\Modules\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function attempt(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!$user->active) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        $this->startSession($user);
        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            $name = session_name();

            if ($name !== false) {
                setcookie(
                    $name,
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function register(array $data): User
    {
        $data['active'] = true;
        return $this->userRepository->create($data);
    }

    public function check(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function user(): ?User
    {
        if (!$this->check()) {
            return null;
        }
        /** @var mixed $rawUserId */
        $rawUserId = $_SESSION['user_id'] ?? null;
        if (!is_numeric($rawUserId)) {
            return null;
        }
        return $this->userRepository->findById((int) $rawUserId);
    }

    public function id(): ?int
    {
        /** @var mixed $rawUserId */
        $rawUserId = $_SESSION['user_id'] ?? null;
        return is_numeric($rawUserId) ? (int) $rawUserId : null;
    }

    protected function startSession(User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email']= $user->email;
    }
}
