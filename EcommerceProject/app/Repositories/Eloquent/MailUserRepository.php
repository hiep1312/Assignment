<?php

namespace App\Repositories\Eloquent;

use App\Models\MailUser;
use App\Repositories\Contracts\MailUserRepositoryInterface;

class MailUserRepository extends BaseRepository implements MailUserRepositoryInterface
{
    public function getModel()
    {
        return MailUser::class;
    }
}
