<?php

namespace App\Providers;

use App\Events\PermissionCreated;
use App\Events\RoleCreated;
use App\Listeners\OnPermissionCreatedListener;
use App\Listeners\OnRoleCreatedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RoleCreated::class => [
            OnRoleCreatedListener::class
        ],
        PermissionCreated::class => [
            OnPermissionCreatedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
