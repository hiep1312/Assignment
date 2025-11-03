<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'recipient_name',
        'phone',
        'province',
        'district',
        'ward',
        'street',
        'postal_code',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getAddressAttribute(): string
    {
        return trim(($this->street ? "{$this->street}, " : '') . "{$this->ward}, {$this->district}, {$this->province}");
    }
}
