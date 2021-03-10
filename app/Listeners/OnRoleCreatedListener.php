<?php

namespace App\Listeners;

use App\Events\RoleCreated;

class OnRoleCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RoleCreated  $event
     * @return void
     */
    public function handle(RoleCreated $event): void
    {
        // Add anything that you'd like to have happen here
    }
}
