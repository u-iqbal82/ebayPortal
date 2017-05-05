<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\BatchAssignedToUser' => [
            'App\Listeners\AssignBatchToUser',
        ],
        'App\Events\UserRestored' => [
            'App\Listeners\CreateUserRestored',
            'App\Listeners\SendUserRestoredNotification',
        ],
        'App\Events\ArticleUpdated' => [
            'App\Listeners\CreateArticleUpdated',
            'App\Listeners\SendBatchCompletedNotification',
        ],
        'App\Events\BatchUpdated' => [
            'App\Listeners\CreateBatchUpdated',
            'App\Listeners\SendBatchUpdatedNotification',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
        'App\Events\BatchAvailableNotification' => [
            'App\Listeners\SendBatchAvailableNotification',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
