<?php

namespace App\Listeners;

use App\Events\ArticleUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Article;
use App\Batch;
use Illuminate\Support\Facades\Event;

class CreateArticleUpdated
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
    }
}
