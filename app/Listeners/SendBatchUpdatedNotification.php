<?php

namespace App\Listeners;

use App\Events\BatchUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Auth;

class SendBatchUpdatedNotification
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
        $batch->name = Auth::user()->name;
        
        Mail::send('emails.batch_updated', ['batch' => $batch], function($message) use ($batch){
            $message->from('admin@ithinkmedia.co.uk', 'iThinkMedia Admin');
            $message->to($batch->user->email, 'iThinkMedia');
            $message->subject('Batch Status Updated');
        });
    }
}
