<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;

class DbCreateCommand extends Command
{
    protected static $defaultName = 'db:create';

    protected function configure(): void
    {
        $this->setDescription('Create the database defined in config/database.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = config('database');
        $conn = $config['connections'][$config['default']];

        if ($conn['driver'] !== 'mysql') {
            $output->writeln("<error>Este comando suporta apenas drivers MySQL no momento.</error>");
            return Command::FAILURE;
        }

        $host = $conn['host'];
        $port = $conn['port'];
        $user = $conn['username'];
        $pass = $conn['password'];
        $db   = $conn['database'];

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
