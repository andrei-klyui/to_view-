<?php

namespace App\Listeners;

use App\Events\UserCreated as UserCreatedEvent;
use App\Mail\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendEmailUserUpdatedToUser
 * @package App\Listeners
 */
class SendEmailUserCreatedToUser
{
    /**
     * @param UserCreatedEvent $event
     */
    public function handle(UserCreatedEvent $event)
    {
        /** @var User $user */
        $user = $event->user;

        Mail::to($user->email)->queue(new UserRegistered($user));
    }
}
