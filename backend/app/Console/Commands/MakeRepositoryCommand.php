<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class for the data access layer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $repositoryName = $name . 'Repository';
        $interfaceName = $name . 'RepositoryInterface';

        $contractsDirectory = app_path('Contracts/Repositories');
        if (!file_exists($contractsDirectory)) {
            mkdir($contractsDirectory, 0755, true);
        }

        $repositoriesDirectory = app_path('Repositories');
        if (!file_exists($repositoriesDirectory)) {
            mkdir($repositoriesDirectory, 0755, true);
        }

        $interfacePath = $contractsDirectory . '/' . $interfaceName . '.php';
        if (!file_exists($interfacePath)) {
            $interfaceContent = "<?php\n\nnamespace App\Contracts\Repositories;\n\ninterface {$interfaceName}\n{\n    // Interface methods\n}\n";
            file_put_contents($interfacePath, $interfaceContent);
        } else {
            $this->error('Interface already exists!');
        }

        $repositoryPath = $repositoriesDirectory . '/' . $repositoryName . '.php';
        if (!file_exists($repositoryPath)) {
            $repositoryContent = "<?php\n\nnamespace App\Repositories;\n\nuse App\Contracts\Repositories\\{$interfaceName};\n\nclass {$repositoryName} implements {$interfaceName}\n{\n    // Repository code\n}\n";
            file_put_contents($repositoryPath, $repositoryContent);
        } else {
            $this->error('Repository already exists!');
        }

        $this->info('Repository and Interface created successfully.');
    }
}
