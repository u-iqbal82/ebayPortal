<?php

namespace App\Listeners;

use App\Events\BatchAssignedToUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignBatchToUser
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
     * @param  BatchAssignedToUser  $event
     * @return void
     */
    public function handle(BatchAssignedToUser $event)
    {
        //
    }
}
