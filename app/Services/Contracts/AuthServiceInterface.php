<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface AuthServiceInterface
{
    public function attempt(string $email, string $password): bool;

    public function logout(): void;

    public function register(array $data): \App\Models\User;

    public function check(): bool;

    public function user(): ?\App\Models\User;

    public function id(): ?int;
}
