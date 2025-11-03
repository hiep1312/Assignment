<?php

namespace App\Services;

use App\Helpers\MailTemplateHelper;
use App\Models\Mail as MailModel;
use App\Models\User;
use App\Repositories\Contracts\MailUserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Mime\Email;
use Throwable;

class MailService
{
    protected MailUserRepositoryInterface $repository;
    protected MailModel $mail;
    protected Collection $recipients;
    protected array $sourceMap = [];

    public function __construct(MailUserRepositoryInterface $repository){
        $this->repository = $repository;
        $this->recipients = (new Collection);
    }

    public function createBatch(MailModel $mail, array $users, array $sources): static
    {
        $this->mail = $mail;
        $batchKey = (string) Str::uuid();
        $payload = array_map(function($user, $source) use ($batchKey){
            $this->sourceMap[$source instanceof User ? $source->id : $source->user_id] = $source;

            return [
                'user_id' => ($user instanceof User ? $user->id : $user),
                'batch_key' => $batchKey,
                'status' => 0
            ];
        }, $users, $sources);

        $this->recipients = $mail->recipients()->createMany($payload)
            ->load('user')
            ->keyBy('user_id');

        return $this;
    }

    public function sendBatch(): int
    {
        if(empty($this->mail)) throw new RuntimeException('Mail model has not been initialized. Please call createBatch() before sendBatch().');

        $deliveryResults = [];

        foreach($this->recipients as $userId => $recipient){
            $updateData = [
                'id' => $recipient->id,
                'user_id' => $recipient->user_id,
                'batch_key' => $recipient->batch_key
            ];

            try {
                $sendResult = Mail::send(
                    [], [],
                    function(Message $message) use ($recipient, $userId){
                        $message->to($recipient->user->email, $recipient->user->name, true)
                            ->subject($this->mail->subject)
                            ->priority(match($this->mail->type){
                                4 => Email::PRIORITY_HIGHEST,
                                1, 2 => Email::PRIORITY_HIGH,
                                default => Email::PRIORITY_LOW
                            });

                        $message->html(MailTemplateHelper::fillPlaceholders($this->mail, $this->sourceMap[$userId], $message), 'utf-8');
                    }
                );

                if(!$sendResult) throw new RuntimeException("Failed to send mail to recipient ID #{$recipient->id}");

                $updateData += [
                    'status' => 1,
                    'sent_at' => now(),
                    'error_message' => null
                ];
            }catch(Throwable $error){
                $updateData += [
                    'status' => 2,
                    'sent_at' => null,
                    'error_message' => $error->getMessage()
                ];
            }

            $deliveryResults[] = $updateData;
        }

        return $this->repository->upsert($deliveryResults, ['id'], ['status', 'sent_at', 'error_message']);
    }

    public function recipients(User|int|null $user = null): Collection|MailModel|null
    {
        if(is_null($user)) return $this->recipients;

        return $this->recipients->get($user instanceof User ? $user->id : $user);
    }
}
