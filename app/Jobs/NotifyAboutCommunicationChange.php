<?php

namespace App\Jobs;

use App\Modules\Customer\AccountSettings\Controllers\AccountSettingsController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyAboutCommunicationChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $old;
    private $new;

    /**
     * Create a new job instance.
     *
     * @param array $old
     * @param array $new
     */
    public function __construct(array $old, array $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            AccountSettingsController::checkChanges($this->old, $this->new);
        } catch (\Exception $e) {
            Log::alert(
                'Error in Job: NotifyAboutCommunicationChange!',
                ['message' => $e->getMessage()]
            );
        }
    }
}
