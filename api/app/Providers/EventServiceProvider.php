<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\UrlLikedNotificationEvent;
use App\Events\NewUrlNotificationEvent;
use App\Events\NewFollowerNotificationEvent;
use App\Listeners\SendNotificationListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UrlLikedNotificationEvent::class => [
            SendNotificationListener::class,
        ],
        NewUrlNotificationEvent::class => [
            SendNotificationListener::class,
        ],
        NewFollowerNotificationEvent::class => [
            SendNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
