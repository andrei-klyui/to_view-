<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Illuminate\Auth\Events\Registered::class => [
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class
        ],
        \App\Events\PostCreated::class => [
            \App\Listeners\SendNotificationToUsers::class
        ],
        \App\Events\PostRead::class => [
            \App\Listeners\ReadNotificationPost::class
        ],
        \App\Events\IssueCreated::class => [
            \App\Listeners\SendNotificationIssueToUsers::class,
            \App\Listeners\SendEmailIssueToUsers::class
        ],
        \App\Events\IssueUpdated::class => [
            \App\Listeners\SendEmailIssueUpdatedToUsers::class
        ],
        \App\Events\IssueRead::class => [
            \App\Listeners\ReadNotificationIssue::class
        ],
        \App\Events\UserUpdated::class => [
            \App\Listeners\SendEmailUserUpdatedToUser::class
        ],
        \App\Events\UserCreated::class => [
            \App\Listeners\SendNotificationCreatedUsers::class,
            \App\Listeners\SendEmailUserCreatedToUser::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
