<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DbSeedCommand extends Command
{
    protected static $defaultName = 'db:seed';

    protected function configure(): void
    {
        $this->setDescription('Run database seeders using Phinx');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phinxBin = BASE_PATH . '/vendor/bin/phinx';
        
        $output->writeln('<info>Running database seeders...</info>');
        
        // Using .bat extension for Windows compatibility if needed, 
        // but Passthru with 'php' prefix is safer
        passthru("php {$phinxBin} seed:run");

        return Command::SUCCESS;
    }
}
