<?php

namespace App\Listeners;

use App\Events\UserRestored;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateUserRestored
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
     * @param  UserRestored  $event
     * @return void
     */
    public function handle(UserRestored $event)
    {
        $user = $event->user;
    }
}
