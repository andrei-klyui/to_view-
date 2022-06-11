<?php

namespace App\Listeners;

use App\Events\UserUpdated as UserUpdatedEvent;
use App\Models\User;
use \App\Mail\UserPasswordChanged;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendEmailUserUpdatedToUser
 * @package App\Listeners
 */
class SendEmailUserUpdatedToUser
{
    /**
     * @param UserUpdatedEvent $event
     */
    public function handle(UserUpdatedEvent $event)
    {
        /** @var User $user */
        $user = $event->user;
        $changes = $user->getChanges();

        if (isset($changes['password'])) {
            Mail::to($user->email)->queue(new UserPasswordChanged($user));
        }
    }
}
