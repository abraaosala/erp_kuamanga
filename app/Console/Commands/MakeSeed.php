<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\PhinxRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSeed extends Command
{
    protected static $defaultName = 'make:seed';
    protected static $defaultDescription = 'Cria um novo seeder';

    public function __construct(
        private PhinxRunner $phinx,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Nome');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $this->phinx->run('seed:create', $name);
        return Command::SUCCESS;
    }
}
