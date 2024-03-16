<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class for the business logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $serviceName = $name . 'Service';
        $interfaceName = $name . 'ServiceInterface';

        $contractsDirectory = app_path('Contracts/Services');
        if (!file_exists($contractsDirectory)) {
            mkdir($contractsDirectory, 0755, true);
        }

        $servicesDirectory = app_path('Services');
        if (!file_exists($servicesDirectory)) {
            mkdir($servicesDirectory, 0755, true);
        }

        $interfacePath = $contractsDirectory . '/' . $interfaceName . '.php';
        if (!file_exists($interfacePath)) {
            $interfaceContent = "<?php\n\nnamespace App\Contracts\Services;\n\ninterface {$interfaceName}\n{\n    // Interface methods\n}\n";
            file_put_contents($interfacePath, $interfaceContent);
        } else {
            $this->error('Interface already exists!');
        }

        $servicePath = $servicesDirectory . '/' . $serviceName . '.php';
        if (!file_exists($servicePath)) {
            $serviceContent = "<?php\n\nnamespace App\Services;\n\nuse App\Contracts\Services\\{$interfaceName};\n\nclass {$serviceName} implements {$interfaceName}\n{\n    // Service code\n}\n";
            file_put_contents($servicePath, $serviceContent);
        } else {
            $this->error('Service already exists!');
        }

        $this->info('Service and its Interface created successfully.');
    }
}
