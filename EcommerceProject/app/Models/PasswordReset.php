<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
