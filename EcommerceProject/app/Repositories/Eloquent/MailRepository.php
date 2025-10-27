<?php

namespace App\Repositories\Eloquent;

use App\Models\Mail;
use App\Repositories\Contracts\MailRepositoryInterface;

class MailRepository extends BaseRepository implements MailRepositoryInterface
{
    public function getModel()
    {
        return Mail::class;
    }
}
