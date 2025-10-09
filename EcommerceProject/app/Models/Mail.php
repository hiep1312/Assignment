<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'variable',
        'type',
    ];

    protected $casts = [
        'variable' => 'array',
        'type' => 'integer'
    ];

    public function recipients()
    {
        return $this->hasMany(MailUser::class, 'mail_id');
    }
}
