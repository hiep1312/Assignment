<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailUser extends Model
{
    protected $fillable = [
        'mail_id',
        'user_id',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'status' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function mail()
    {
        return $this->belongsTo(Mail::class, 'mail_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
