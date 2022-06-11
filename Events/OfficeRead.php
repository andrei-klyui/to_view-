<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use \App\Models\Office;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OfficeRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $officeId;

    /**
     * Create a new event instance.
     * @param int $officeId
     */
    public function __construct(int $officeId)
    {
        $this->officeId = $officeId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'office.read';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('office-channel');
    }
}
