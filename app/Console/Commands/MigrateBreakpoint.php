<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\PhinxRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateBreakpoint extends Command
{
    protected static $defaultName = 'breakpoint';
    protected static $defaultDescription = 'Gere breakpoints de migração';

    public function __construct(
        private PhinxRunner $phinx,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->phinx->run('breakpoint');
        return Command::SUCCESS;
    }
}
