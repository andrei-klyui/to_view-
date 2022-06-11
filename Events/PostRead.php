<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use \App\Models\Post;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;

    /**
     * Create a new event instance.
     * @param int $postId
     */
    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'post.read';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('post-channel');
    }
}
