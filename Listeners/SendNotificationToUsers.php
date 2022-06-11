<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendNotificationToUsers
{
    /**
     * Handle the event.
     *
     * @param  PostCreated  $event
     */
    public function handle(PostCreated $event)
    {
        $users = User::get()->all();

        /** @var Post $post */
        $post = $event->post;

        Notification::send($users, new \App\Notifications\PostCreated($post));
    }
}
