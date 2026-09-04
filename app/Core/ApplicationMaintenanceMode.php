<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Contracts\Foundation\MaintenanceMode;

class ApplicationMaintenanceMode implements MaintenanceMode
{
    protected bool $isActive = false;

    /** @var array<string, mixed> */
    protected array $data = [];

    /**
     * @param array<string, mixed> $payload
     */
    public function activate(array $payload): void
    {
        $this->isActive = true;
        $this->data = $payload;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function active(): bool
    {
        return $this->isActive;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }
}
