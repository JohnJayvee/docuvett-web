<?php

namespace App\Jobs;

use App\Contracts\SchedulesNotificationsInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendMailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mailable;
    private $email;

    /**
     * Create a new job instance.
     * SendMailable constructor.
     * @param Mailable $mailable
     * @param SchedulesNotificationsInterface|null $email
     */
    public function __construct(Mailable $mailable, SchedulesNotificationsInterface $email = null)
    {
        $this->mailable = $mailable;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Mail::send($this->mailable);
            if ($this->email) {
                $this->email->sent_at = Carbon::now();
                $this->email->status = $this->email::STATUS_SUCCESS;
                $this->email->save();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            if ($this->email) {
                $this->email->status = $this->email::STATUS_ERROR;
                $this->email->save();
            }
        }
    }
}
