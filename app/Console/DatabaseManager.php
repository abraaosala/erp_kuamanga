<?php

declare(strict_types=1);

namespace App\Console;

use PDO;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DatabaseManager
{
    private ?PDO $pdo = null;

    public function assertValidName(string $name): void
    {
        if ($name === '') {
            throw new RuntimeException('DB_DATABASE não pode ser vazio.');
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new RuntimeException('Nome de base de dados inválido.');
        }
    }

    public function exists(string $name): bool
    {
        $stmt = $this->pdo()->query(
            'SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ' . $this->pdo()->quote($name)
        );

        return $stmt !== false && (bool) $stmt->fetchColumn();
    }

    public function create(string $name): void
    {
        $this->pdo()->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $name
        ));
    }

    public function createOrFail(string $name, InputInterface $input, OutputInterface $output): void
    {
        if ($this->exists($name)) {
            return;
        }

        $helper = new QuestionHelper();
        $question = new ConfirmationQuestion(
            sprintf("Base de dados '%s' n\u{00e3}o existe. Criar? [Y/n] ", $name),
            true,
        );

        if (!$helper->ask($input, $output, $question)) {
            throw new RuntimeException('Cancelado.');
        }

        $this->create($name);
        $output->writeln("<info>Base de dados '{$name}' criada.</info>");
    }

    public function drop(string $name): void
    {
        $this->pdo()->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $name));
    }

    private function pdo(): PDO
    {
        if ($this->pdo === null) {
            $driver = env('DB_CONNECTION', 'mysql');

            if ($driver !== 'mysql') {
                throw new RuntimeException("Apenas o driver 'mysql' é suportado.");
            }

            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', 3306);
            $user = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            $this->pdo = new PDO(
                sprintf(
                    'mysql:host=%s;port=%d;charset=utf8mb4',
                    is_string($host) ? $host : '127.0.0.1',
                    is_numeric($port) ? (int) $port : 3306,
                ),
                is_string($user) ? $user : 'root',
                is_string($password) ? $password : '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );
        }

        return $this->pdo;
    }
}
