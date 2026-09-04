<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\PhinxRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'seed:run', description: 'Executa os seeders')]
class DbSeed extends Command
{
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
            $value = $input->getOption($opt);
            if (is_string($value) && $value !== '') {
                $extra[] = sprintf('--%s=%s', $opt, escapeshellarg($value));
            }
        }

        $seeds = $input->getOption('seed');
        if (is_array($seeds)) {
            foreach ($seeds as $seed) {
                if (is_string($seed)) {
                    $extra[] = sprintf('--seed=%s', escapeshellarg($seed));
                }
            }
        }

        if ($input->getOption('no-info')) {
            $extra[] = '--no-info';
        }

        $this->phinx->run('seed:run', null, $extra);
        return Command::SUCCESS;
    }
}
