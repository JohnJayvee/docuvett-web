<?php

namespace App\Jobs;

use App\Events\SubscriptionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class NotifyAboutSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var null|string
     */
    private $status;


    /**
     * Create a new job instance.
     * @param int $userId
     * @param string $message
     * @param null|string $status
     */
    public function __construct(int $userId, string $message, ?string $status = 'success')
    {
        $this->userId = $userId;
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
            broadcast(new SubscriptionNotification($this->userId, $this->message, $this->status));
        } catch (\Exception $e) {
            Log::alert(
                'Error in Job: NotifyAboutSubscription!',
                ['message' => $e->getMessage()]
            );
        }
    }
}
