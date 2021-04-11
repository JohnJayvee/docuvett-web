<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModelMetaGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:model-meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "'Short way to generate Model's meta'";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $model = $this->ask("Model's name");

        $this->call('ide-helper:models', [
            'model' => ["App\Models\\$model\\$model"],
            '--reset' => true,
            '--write' => true,
        ]);

        return 0;
    }
}
