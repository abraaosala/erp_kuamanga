<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use PDO;

class DbDropCommand extends Command
{
    protected static ?string $defaultName = 'db:drop';

    protected function configure(): void
    {
        $this->setDescription('Drop the database defined in config/database.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        /** @var QuestionHelper $helper */
        $question = new ConfirmationQuestion('<question>Tem certeza que deseja DROPAR o banco de dados? Todos os dados serão perdidos! (y/n)</question> ', false);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<info>Operação cancelada.</info>');
            return Command::SUCCESS;
        }

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
