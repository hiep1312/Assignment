<?php

namespace App\Listeners;

use App\Events\NotificationSentEvent;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationListener implements ShouldQueueAfterCommit
{
    public $tries = 2;
    public $delay = 5;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSentEvent $event): void
    {

    }
}
