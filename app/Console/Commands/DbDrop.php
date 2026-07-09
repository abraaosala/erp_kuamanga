<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\DatabaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DbDrop extends Command
{
    protected static $defaultName = 'db:drop';
    protected static $defaultDescription = 'Elimina a base de dados definida em DB_DATABASE';

    public function __construct(
        private DatabaseManager $db,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $database = (string) env('DB_DATABASE', '');
        $this->db->assertValidName($database);
        $this->db->drop($database);

        $output->writeln("<info>Base de dados '{$database}' eliminada (se existia).</info>");
        return Command::SUCCESS;
    }
}
