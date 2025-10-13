<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model
{
    use HasFactory, SoftDeletes;

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
