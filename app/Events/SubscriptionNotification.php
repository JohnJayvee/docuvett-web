<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $status;
    private $userId;

    /**
     * Create a new event instance.
     *
     * @param $userId
     * @param string $message
     * @param string $status ['success', 'warning', 'info', 'error']
     */
    public function __construct($userId, $message, $status = 'success')
    {
        $this->userId  = $userId;
        $this->message = $message;
        $this->status  = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        try {
            return new PrivateChannel('communication-channel.' . $this->userId);
        } catch (\Exception $e) {
            return ['Error' => $e->getMessage()];
        }
    }
}
