<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use PDO;

class DbDropCommand extends Command
{
    protected static $defaultName = 'db:drop';

    protected function configure(): void
    {
        $this->setDescription('Drop the database defined in config/database.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('<question>Tem certeza que deseja DROPAR o banco de dados? Todos os dados serão perdidos! (y/n)</question> ', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Operação cancelada.</info>');
            return Command::SUCCESS;
        }

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

            $output->writeln("<info>Dropando banco de dados '{$db}' em {$host}...</info>");
            $pdo->exec("DROP DATABASE IF EXISTS `{$db}`");
            
            $output->writeln("<info>Banco de dados '{$db}' removido com sucesso.</info>");
            return Command::SUCCESS;
        } catch (\PDOException $e) {
            $output->writeln("<error>Erro ao remover banco de dados: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
