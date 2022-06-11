<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Issue;

class IssueRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $issueId;

    /**
     * Create a new event instance.
     * @param int $issueId
     * @return void
     */
    public function __construct(int $issueId)
    {
        $this->issueId = $issueId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'issue.read';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('issue-channel');
    }
}
