<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Console;

use App\Console\Commands\ModelMetaGenerate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ModelMetaGenerate::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     * @noinspection PhpIncludeInspection
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
