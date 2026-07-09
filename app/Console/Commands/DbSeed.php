<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\PhinxRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbSeed extends Command
{
    protected static $defaultName = 'seed:run';
    protected static $defaultDescription = 'Executa os seeders';

    public function __construct(
        private PhinxRunner $phinx,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('seed', 's', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Nome do seeder (múltiplos permitidos)')
            ->addOption('environment', 'e', InputOption::VALUE_REQUIRED, 'Ambiente alvo')
            ->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'Ficheiro de configuração')
            ->addOption('parser', 'p', InputOption::VALUE_REQUIRED, 'Parser de configuração')
            ->addOption('no-info', null, InputOption::VALUE_NONE, 'Oculta informações de debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extra = [];

        foreach (['environment', 'configuration', 'parser'] as $opt) {
            if ($value = $input->getOption($opt)) {
                $extra[] = sprintf('--%s=%s', $opt, escapeshellarg($value));
            }
        }

        foreach ($input->getOption('seed') as $seed) {
            $extra[] = sprintf('--seed=%s', escapeshellarg($seed));
        }

        if ($input->getOption('no-info')) {
            $extra[] = '--no-info';
        }

        $this->phinx->run('seed:run', null, $extra);
        return Command::SUCCESS;
    }
}
