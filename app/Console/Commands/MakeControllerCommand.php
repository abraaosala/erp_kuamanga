<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeControllerCommand extends Command
{
    protected static $defaultName = 'make:controller';

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new controller class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        $path = BASE_PATH . "/app/Http/Controllers/Modules/User/{$name}.php"; // Default to User module for now

        if (file_exists($path)) {
            $output->writeln("<error>Controller {$name} already exists!</error>");
            return Command::FAILURE;
        }

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\User;

use App\Core\Application;
use Illuminate\Http\Request;

class {$name}
{
    public function index(Request \$request)
    {
        //
    }
}
PHP;

        file_put_contents($path, $content);

        $output->writeln("<info>Controller {$name} created successfully!</info>");
        return Command::SUCCESS;
    }
}
