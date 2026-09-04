<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\StubService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'make:view', description: 'Cria uma View Blade')]
class MakeView extends Command
{
    public function __construct(
        private StubService $stubs,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Nome(s) da view (ex: auth.login)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array<int, string> $names */
        $names = $input->getArgument('name');
        foreach ($names as $name) {
            $path = dirname(__DIR__, 3) . '/resources/views/' . str_replace('.', '/', $name) . '.blade.php';

            $content = $this->stubs->renderStub('view', []);

            $this->stubs->putClassFile($path, $content, $output);
        }

        return Command::SUCCESS;
    }
}
