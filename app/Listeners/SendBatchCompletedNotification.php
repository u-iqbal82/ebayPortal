<?php

namespace App\Listeners;

use App\Events\ArticleUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Batch;

class SendBatchCompletedNotification
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
     * @param  ArticleUpdated  $event
     * @return void
     */
    public function handle(ArticleUpdated $event)
    {
        $article = $event->article;
        
        $batch = Batch::find($article->batch_id);
        
        $updateStatusToFinal = TRUE;
        foreach($batch->articles as $article)
        {
            if ($article->status != 'QualityChecked')
            {
                $updateStatusToFinal = FALSE;
            }
        }
        
        
        if ($updateStatusToFinal)
        {
            $batch->status = 'Final';
            $batch->save();
            
            Mail::send('emails.batch_updated', ['batch' => $batch], function($message) use ($batch){
                $message->from('admin@ithinkmedia.co.uk', 'iThinkMedia Admin');
                $message->to($batch->user->email, 'iThinkMedia');
                $message->subject('Batch Status Updated');
            });
        }
        else
        {
            $batch->status = 'QCInProcess';
            $batch->save();
        }
    }
}
