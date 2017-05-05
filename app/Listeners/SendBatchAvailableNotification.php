<?php

namespace App\Listeners;

use App\Events\BatchAvailableNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;

use App\Batch;
use App\Article;
use App\User;

class SendBatchAvailableNotification
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
     * @param  BatchAvailableNotification  $event
     * @return void
     */
    public function handle(BatchAvailableNotification $event)
    {
        $batch = $event->batch;
        
        $batchDb = DB::table('batches')
            ->join('articles', 'batches.id', '=', 'articles.batch_id')
            ->join('article_user', 'articles.id', '=', 'article_user.article_id')
            ->select('batches.*', 'articles.*', 'article_user.*')
            ->where('batches.id', '=', $batch->id)
            ->get();
            
        //dd($batchDb);
            
        $userIds = [];    
        foreach($batchDb as $b)
        {
            $userIds[$b->user_id] = false;
        }
        
        if (count($userIds) > 0)
        {
            foreach($userIds as $userId => $value)
            {
                //$userIds[$userId] = User::find($userId)->email;
                $user = User::find($userId);
                
                Mail::send('emails.batch_available', ['batch' => $batch, 'user', $user], function($message) use ($batch, $user){
                    $message->from('admin@ithinkmedia.co.uk', 'iThinkMedia Admin');
                    $message->to($user->email);
                    $message->subject('Batch Available');
            });
            }
        }
        
        
    }
}
