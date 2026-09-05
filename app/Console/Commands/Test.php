<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test', description: 'Runs the test suite (Pest)')]
class Test extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Runs the test suite (Pest)')
            ->addArgument('filter', InputArgument::OPTIONAL, 'Filtro para correr apenas testes que correspondam (ex: Schedule)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $base = defined('BASE_PATH') ? BASE_PATH : getcwd();
        $pest = $base . '/vendor/bin/pest';

        if (!is_file($pest)) {
            $output->writeln('<error>Pest não encontrado. Rode `composer install` primeiro.</error>');
            return Command::FAILURE;
        }

        $filter = $input->getArgument('filter');
        $args = is_string($filter) && $filter !== '' ? sprintf(' --filter=%s', escapeshellarg($filter)) : '';

        $cmd = sprintf('"%s" "%s"%s', PHP_BINARY, $pest, $args);

        $descriptors = [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ];

        $process = proc_open($cmd, $descriptors, $pipes);
        if (!is_resource($process)) {
            $output->writeln('<error>Não foi possível iniciar o Pest.</error>');
            return Command::FAILURE;
        }

        $exitCode = proc_close($process);

        return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}