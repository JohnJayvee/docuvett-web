<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Email: gp.neutron@gmail.com
 */

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleStatusNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    private $module;

    /**
     * Create a new event instance.
     *
     * @param string $module
     * @param string $message
     * @param string $status ['success', 'warning', 'info', 'error']
     */
    public function __construct($module, $message, $status = 'success')
    {
        $this->module = $module;
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
            return new Channel('system-channel');
        } catch (\Exception $e) {
            return ['Error' => $e->getMessage()];
        }
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'module' => $this->module,
            'message' => $this->message,
            'status' => $this->status
        ];
    }
}