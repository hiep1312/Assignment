<?php

namespace App\Listeners;

use App\Events\MailSentEvent;
use App\Events\NotificationSentEvent;
use App\Models\Mail;
use App\Repositories\Contracts\MailRepositoryInterface;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use RuntimeException;
use Throwable;

class SendMailListener implements ShouldQueue
{
    public $tries = 3;
    public $delay = 10;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected MailRepositoryInterface $repository,
        protected MailService $service
    ){}

    /**
     * Handle the event.
     */
    public function handle(MailSentEvent $event): void
    {
        $mail = $event->mailSource instanceof Mail
            ? $event->mailSource
            : $this->repository->first(criteria: function($query) use ($event){
                $query->where('type', $event->mailSource);
            }, columns: ['*'], throwNotFound: false);

        if($mail){
            $this->service->createBatch($mail, $event->users, $event->payload)
                ->sendBatch();
        }else{
            $mailTypeName = match ($event->mailSource) {
                0 => 'Custom Mail',
                1 => 'Order Update Mail',
                2 => 'Payment Update Mail',
                3 => 'Promotion Mail',
                4 => 'Account Update Mail',
                5 => 'Maintenance Mail',
                6 => 'Internal System Mail',
                default => 'Unknown Mail Type',
            };

            throw new RuntimeException("The system cannot find a configured mail for type '{$mailTypeName}'.");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(MailSentEvent $event, Throwable $error): void
    {
        event(new NotificationSentEvent(
            6,
            $event->users,
            (object) [
                'title' => 'Mail Delivery Failed',
                'details' => $error->getMessage(),
                'time' => now(),
                'affected_users' => $event->users,
                'affected_count' => count($event->users),
                'severity_level' => 'High'
            ]
        ));
    }
}
