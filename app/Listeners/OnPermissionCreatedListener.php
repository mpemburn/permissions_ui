<?php

namespace App\Listeners;

use App\Events\PermissionCreated;

class OnPermissionCreatedListener
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
     * @param  PermissionCreated  $event
     * @return void
     */
    public function handle(PermissionCreated $event): void
    {
        // Add anything that you'd like to have happen here
    }
}
