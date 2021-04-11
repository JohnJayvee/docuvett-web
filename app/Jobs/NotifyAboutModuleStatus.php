<?php

namespace App\Jobs;

use App\Events\ModuleStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class NotifyAboutModuleStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $message;

    /**
     * @var null|string
     */
    private $status;

    /**
     * @var string
     */
    private $module;

    /**
     * Create a new job instance.
     * @param string $module
     * @param string $message
     * @param null|string $status
     */
    public function __construct(string $module, string $message, ?string $status = 'success')
    {
        $this->module = $module;
        $this->message = $message;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            broadcast(new ModuleStatusNotification($this->module, $this->message, $this->status));
        } catch (\Exception $e) {
            Log::alert(
                'Error in Job: NotifyAboutModuleStatus!',
                ['message' => $e->getMessage()]
            );
        }
    }
}
