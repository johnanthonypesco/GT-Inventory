<?php

namespace App\Providers;

// 1. Add these 'use' statements at the top
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use App\Listeners\UserAuthenticationListener;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
    /**
     * The event listener mappings for the application.
     * Add the new mapping for your chat message email notification here.
     */

     'App\Events\OrderPlaced' => [
        'App\Listeners\SendOrderNotification',
    ],
    'App\Events\NewMessageReceived' => [
        'App\Listeners\SendNewMessageNotification',
    ],

    // Your existing listeners below
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],

    Login::class => [
        UserAuthenticationListener::class,
    ],
    Failed::class => [
        UserAuthenticationListener::class,
    ],
    Logout::class => [
        UserAuthenticationListener::class,
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