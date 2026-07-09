<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\PhinxRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateStatus extends Command
{
    protected static $defaultName = 'status';
    protected static $defaultDescription = 'Mostra o estado das migrações';

    public function __construct(
        private PhinxRunner $phinx,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->phinx->run('status');
        return Command::SUCCESS;
    }
}
