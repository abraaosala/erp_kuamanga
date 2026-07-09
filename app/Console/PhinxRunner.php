<?php

declare(strict_types=1);

namespace App\Console;

class PhinxRunner
{
    public function run(string $action, ?string $name = null, array $extra = []): void
    {
        $phinx = escapeshellarg(dirname(__DIR__, 2) . '/vendor/bin/phinx');
        $command = "php {$phinx} {$action}";

        if ($name) {
            $command .= ' ' . escapeshellarg($name);
        }

        foreach ($extra as $flag) {
            $command .= ' ' . $flag;
        }

        passthru($command);
    }
}
