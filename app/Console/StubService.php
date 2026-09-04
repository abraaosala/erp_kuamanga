<?php

declare(strict_types=1);

namespace App\Console;

use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class StubService
{
    public function studly(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', $value);
        $value = ucwords(strtolower($value));
        return str_replace(' ', '', $value);
    }

    /**
     * @param array<string, string> $replacements
     */
    public function renderStub(string $stubName, array $replacements): string
    {
        $stubPath = dirname(__DIR__, 2) . '/stubs/' . $stubName . '.stub';

        if (!file_exists($stubPath)) {
            throw new RuntimeException("Stub não encontrado: {$stubPath}");
        }

        $content = file_get_contents($stubPath);

        if ($content === false) {
            throw new RuntimeException("Não foi possível ler o stub: {$stubPath}");
        }

        foreach ($replacements as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }

        return $content;
    }

    public function putClassFile(string $path, string $content, OutputInterface $output): void
    {
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }

        if (file_exists($path)) {
            $output->writeln("<comment>Ficheiro já existe: {$path}</comment>");
            return;
        }

        file_put_contents($path, $content);
        $output->writeln("<info>Criado: {$path}</info>");
    }
}
