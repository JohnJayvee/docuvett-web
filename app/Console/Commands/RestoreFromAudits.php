<?php

namespace App\Console\Commands;

use App\Models\Audit\Audit;
use Illuminate\Console\Command;

class RestoreFromAudits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audits:restore {date_from} {date_to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore deleted models from audits';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Fetch models to restore');
        $modelsToRestore = Audit
            ::query()
            ->select(['auditable_type', 'old_values'])
            ->where('event', '=', 'deleted')
            ->whereBetween('created_at', [$this->argument('date_from'), $this->argument('date_to')])
            ->get();

        $countAlreadyExistingEntities = 0;
        $bar = $this->output->createProgressBar(count($modelsToRestore));
        $bar->start();
        foreach ($modelsToRestore as $modelToRestore) {
            $oldValues = (array)$modelToRestore->old_values;
            unset($oldValues['id']);
            $query = $modelToRestore->auditable_type::query();
            foreach ($oldValues as $fieldName => $oldValue) {
                $query->where($fieldName, '=', $oldValue);
            }
            $isEntityExist = $query->exists();
            if ($isEntityExist) {
                $countAlreadyExistingEntities++;
                $bar->advance();
                continue;
            }
            $modelToRestore->auditable_type::create((array)$modelToRestore->old_values);
            $bar->advance();
        }
        $bar->finish();
        $this->info(PHP_EOL);
        if ($countAlreadyExistingEntities > 0) {
            $this->info($countAlreadyExistingEntities . ' entities already existed');
        }
        $this->info('Restore finished');
    }
}
