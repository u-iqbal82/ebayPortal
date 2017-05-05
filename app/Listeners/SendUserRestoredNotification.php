<?php

namespace App\Listeners;

use App\Events\UserRestored;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendUserRestoredNotification
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
        
        Mail::send('emails.user_restored', ['user' => $user], function($message) use ($user){
            $message->from('admin@ithinkmedia.co.uk', 'iThinkMedia Admin');
            $message->to($user->email, $user->name);
            $message->subject('User Activation');
        });
    }
}
