<?php

namespace App\Listeners;

use App\Events\MailSentEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendMailListener implements ShouldQueue
{
    public $tries = 3;
    public $delay = 10;

    /**
     * Create the event listener.
     */
    public function __construct(){

    }

    /**
     * Handle the event.
     */
    public function handle(MailSentEvent $event): void
    {


    }

    /**
     * Handle a job failure.
     */
    public function failed(MailSentEvent $event, Throwable $exception): void
    {
        //
    }
}
