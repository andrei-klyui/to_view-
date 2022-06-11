<?php

namespace App\Listeners;

use App\Events\UserCreated as UserCreatedEvent;
use App\Models\user;
use Illuminate\Support\Facades\Notification;
use App\Repositories\UserRepository;

/**
 * Class SendNotificationCreatedUsers
 * @package App\Listeners
 */
class SendNotificationCreatedUsers
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     *
     * SendNotificationCreatedUsers constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param UserCreatedEvent $event
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        /** @var User $user */
        $user = $event->user;

        $data = $this->userRepository->getUserByRole(User::SUPER_ADMIN);

        Notification::send($data, new \App\Notifications\UserCreated($user));
    }
}
