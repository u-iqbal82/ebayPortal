<?php

namespace App\Listeners;

use App\Events\BatchUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateBatchUpdated
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
     * @param  BatchUpdated  $event
     * @return void
     */
    public function handle(BatchUpdated $event)
    {
        $batch = $event->batch;
    }
}
