<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure(): void
    {
        $this
            ->setDescription('Run database migrations using Phinx')
            ->addOption('rollback', 'r', InputOption::VALUE_NONE, 'Rollback the last migration')
            ->addOption('seed', 's', InputOption::VALUE_NONE, 'Run seeders after migrations')
            ->addOption('fresh', 'f', InputOption::VALUE_NONE, 'Rollback all and re-run all migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phinxBin = BASE_PATH . '/vendor/bin/phinx';

        if ($input->getOption('fresh')) {
            $output->writeln('<info>Rolling back all migrations...</info>');
            passthru("php {$phinxBin} rollback -t 0");
        }

        if ($input->getOption('rollback')) {
            $output->writeln('<info>Rolling back last migration...</info>');
            passthru("php {$phinxBin} rollback");
            return Command::SUCCESS;
        }

        $output->writeln('<info>Running migrations...</info>');
        passthru("php {$phinxBin} migrate");

        if ($input->getOption('seed')) {
            $output->writeln('<info>Running seeders...</info>');
            passthru("php {$phinxBin} seed:run");
        }

        return Command::SUCCESS;
    }
}
