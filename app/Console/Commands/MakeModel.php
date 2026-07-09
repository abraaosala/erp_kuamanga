<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\StubService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModel extends Command
{
    protected static $defaultName = 'make:model';
    protected static $defaultDescription = 'Cria um Model Eloquent';

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
            $table = strtolower($class) . 's';

            $path = dirname(__DIR__, 3) . "/app/Infrastructure/Persistence/Eloquent/{$class}.php";

            $content = $this->stubs->renderStub('model', [
                'namespace' => 'App\\Infrastructure\\Persistence\\Eloquent',
                'class' => $class,
                'table' => $table,
            ]);

            $this->stubs->putClassFile($path, $content, $output);
        }

        return Command::SUCCESS;
    }
}
