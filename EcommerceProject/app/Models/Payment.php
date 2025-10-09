<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'method',
        'status',
        'amount',
        'transaction_id',
        'transaction_data',
        'paid_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'amount' => 'integer',
        'transaction_data' => 'object',
        'paid_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
