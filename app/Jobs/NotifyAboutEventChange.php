<?php

namespace App\Jobs;

use App\Libraries\Notification\ExtNotification;
use App\Libraries\Utils\Utils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyAboutEventChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $message = '';
    private $connections = [];
    private $is_sms = false;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param array $connections
     * @param bool $is_sms
     */
    public function __construct($message, $connections, $is_sms = false)
    {
        $this->is_sms      = $is_sms;
        $this->message     = $message;
        $this->connections = $connections;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            foreach ($this->connections as $connection) {
                if ($this->is_sms) {
                    ExtNotification::sendSMS(
                        $this->message,
                        [$connection->phone],
                        [],
                        $connection->pool->sms
                    );
                } else {
                    ExtNotification::sendEmail(
                        'Event data changed',
                        ['' => $connection->email],
                        '',
                        nl2br($this->message) . Utils::getSystemEmailFooter(),
                        '',
                        $connection->owner->displayName,
                        $connection->pool->email,
                        $connection->owner->displayName
                    );
                }
            }
        } catch (\Exception $e) {
            Log::alert(
                'Error in Job: NotifyAboutEventChange!',
                ['message' => $e->getMessage()]
            );
        }
    }
}
