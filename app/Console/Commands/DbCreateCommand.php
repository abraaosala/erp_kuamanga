<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;

class DbCreateCommand extends Command
{
    protected static ?string $defaultName = 'db:create';

    protected function configure(): void
    {
        $this->setDescription('Create the database defined in config/database.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array{default: string, connections: array<string, array<string, mixed>>} $config */
        $config = config('database');
        $conn = $config['connections'][$config['default']];

        if (!is_string($conn['driver']) || $conn['driver'] !== 'mysql') {
            $output->writeln("<error>Este comando suporta apenas drivers MySQL no momento.</error>");
            return Command::FAILURE;
        }

        $host = is_string($conn['host']) ? $conn['host'] : '127.0.0.1';
        $port = is_numeric($conn['port']) ? (int) $conn['port'] : 3306;
        $user = is_string($conn['username']) ? $conn['username'] : '';
        $pass = is_string($conn['password']) ? $conn['password'] : '';
        $db   = is_string($conn['database']) ? $conn['database'] : '';

        try {
            $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $output->writeln("<info>Criando banco de dados '{$db}' em {$host}...</info>");
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $output->writeln("<info>Banco de dados '{$db}' criado ou já existente.</info>");
            return Command::SUCCESS;
        } catch (\PDOException $e) {
            $output->writeln("<error>Erro ao criar banco de dados: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
