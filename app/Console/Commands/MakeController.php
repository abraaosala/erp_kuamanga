<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\StubService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Command
{
    protected static $defaultName = 'make:controller';
    protected static $defaultDescription = 'Cria um Controller';

    public function __construct(
        private StubService $stubs,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Nome(s) da classe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($input->getArgument('name') as $name) {
            $class = $this->stubs->studly($name);

            if (!str_ends_with($class, 'Controller')) {
                $class .= 'Controller';
            }

            $path = dirname(__DIR__, 3) . "/app/Http/Controllers/{$class}.php";

            $content = $this->stubs->renderStub('controller', [
                'namespace' => 'App\\Http\\Controllers',
                'class' => $class,
            ]);

            $this->stubs->putClassFile($path, $content, $output);
        }

        return Command::SUCCESS;
    }
}
