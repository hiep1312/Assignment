<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'variable',
    ];

    protected $casts = [
        'type' => 'integer',
        'variable' => 'array',
    ];

    public function recipients()
    {
        return $this->hasMany(NotificationUser::class, 'notification_id');
    }
}
