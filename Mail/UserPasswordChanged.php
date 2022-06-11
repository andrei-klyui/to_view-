<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserPasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User */
    protected $user;

    /**
     * UserRegistered constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('mails.user.password-changed')
            ->with(['name' => $this->user->name]);
    }
}
