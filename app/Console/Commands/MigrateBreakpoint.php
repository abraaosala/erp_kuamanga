<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\PhinxRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'breakpoint', description: 'Gere breakpoints de migração')]
class MigrateBreakpoint extends Command
{
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
