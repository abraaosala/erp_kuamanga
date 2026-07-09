<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\StubService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRepository extends Command
{
    protected static $defaultName = 'make:repository';
    protected static $defaultDescription = 'Cria um Repository';

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

            if (!str_ends_with($class, 'Repository')) {
                $class .= 'Repository';
            }

            $path = dirname(__DIR__, 3) . "/app/Infrastructure/Persistence/Repositories/{$class}.php";

            $content = $this->stubs->renderStub('repository', [
                'namespace' => 'App\\Infrastructure\\Persistence\\Repositories',
                'class' => $class,
            ]);

            $this->stubs->putClassFile($path, $content, $output);
        }

        return Command::SUCCESS;
    }
}
