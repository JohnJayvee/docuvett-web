<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleMakeCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:module {name} 
                                {--all       : Additionally create Migration and VueComponent} 
                                {--migration : Additionally create only Migration} 
                                {--vue       : Additionally create only VueComponent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Controller, Routes and Model for new Module';

    /**
     * Create a new controller creator command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->input->setOption('migration', true);
            $this->input->setOption('vue', true);
        }

        $this->createModel();
        $this->createController();

        if ($this->option('migration')) {
            $this->createMigration();
        }
        if ($this->option('vue')) {
            $this->createVueComponent();
        }
    }

    /**
     * Create a model file for the module.
     *
     * @return void
     */
    protected function createModel()
    {
        $model = Str::singular(Str::studly(class_basename($this->argument('name'))));

        $this->call('make:model', [
            'name' => "Models\\{$model}\\{$model}"
        ]);
    }

    /**
     * Create a migration file for the module.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

        try {
            $this->call('make:migration', [
                'name' => "create_{$table}_table",
                '--create' => $table,
            ]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Create a controller for the module.
     *
     * @return void
     * @throws FileNotFoundException
     */
    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = Str::singular(Str::studly(class_basename($this->argument('name'))));

        $path = $this->getControllerPath($this->argument('name'));

        if ($this->alreadyExists($path)) {
            $this->error('Controller already exists!');
        } else {
            $this->makeDirectory($path);

            $stub = $this->files->get(base_path('resources/stubs/controller.model.api.stub'));

            $stub = str_replace(
                [
                    'DummyNamespace',
                    'DummyRootNamespace',
                    'DummyClass',
                    'DummyFullModelClass',
                    'DummyModelClass',
                    'DummyModelVariable',
                ],
                [
                    "App\Modules\\".trim($this->argument('name'))."\Controllers",
                    $this->laravel->getNamespace(),
                    $controller.'Controller',
                    "App\Models\\{$modelName}\\{$modelName}",
                    $modelName,
                    lcfirst(($modelName))
                ],
                $stub
            );

            $this->files->put($path, $stub);
            $this->info('Controller created successfully.');
            $this->updateModularConfig();
        }

        // create routes_api.php

        $routePath = $this->getRoutesPath($this->argument('name'));

        if ($this->alreadyExists($routePath)) {
            $this->error('Routes already exists!');
        } else {
            $stub = $this->files->get(base_path('resources/stubs/routes.api.stub'));

            $stub = str_replace(
                [
                    'DummyClass',
                    'DummyRoutePrefix',
                    'DummyModelVariable',
                ],
                [
                    $controller.'Controller',
                    Str::plural(Str::snake(lcfirst($modelName), '-')),
                    lcfirst($modelName)
                ],
                $stub
            );

            $this->files->put($routePath, $stub);
            $this->info('Routes created successfully.');
        }
    }

    /**
     * Create a Vue component file for the module.
     *
     * @return void
     * @throws FileNotFoundException
     */
    protected function createVueComponent()
    {
        $path = $this->getVueComponentPath($this->argument('name'));

        $component = Str::studly(class_basename($this->argument('name')));

        if ($this->alreadyExists($path)) {
            $this->error('Vue Component already exists!');
        } else {
            $this->makeDirectory($path);

            $stub = $this->files->get(base_path('resources/stubs/vue.component.stub'));

            $stub = str_replace(
                [
                    'DummyClass',
                ],
                [
                    $component,
                ],
                $stub
            );

            $this->files->put($path, $stub);
            $this->info('Vue Component created successfully.');
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('resources/stubs/model.stub');
    }

    /**
     * Update config/modular.php file with new module.
     *
     * @return string
     * @throws FileNotFoundException
     */
    protected function updateModularConfig()
    {
        $group = explode('\\', $this->argument('name'))[0];
        $module = Str::studly(class_basename($this->argument('name')));

        $modular = $this->files->get(base_path('config/modular.php'));

        $matches = [];

        preg_match("/'{$group}' => \[(.*?)\]/sm", $modular, $matches);

        if (count($matches) == 2) {
            if (!preg_match("/'{$module}'/", $matches[1])) {
                $parts = preg_split("/('{$group}' => \[)/", $modular, 2, PREG_SPLIT_DELIM_CAPTURE);
                if (count($parts) == 3) {
                    $config = $parts[0].$parts[1]."\n            '$module',".$parts[2];

                    $this->files->put(base_path('config/modular.php'), $config);
                }
            }
        }
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getControllerPath($name)
    {
        $controller = Str::studly(class_basename($name));
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $name)."/Controllers/{$controller}Controller.php";
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getRoutesPath($name)
    {
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $name)."/routes_api.php";
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getVueComponentPath($name)
    {
        return base_path('resources/assets/js/views/'.str_replace('\\', '/', $name).".vue");
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $path
     * @return bool
     */
    protected function alreadyExists($path)
    {
        return $this->files->exists($path);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}
