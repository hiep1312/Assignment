<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
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

    public function canUpdate(): bool
    {
        $order = $this->loadMissing('order')->order;
        return $order->status < OrderStatus::SHIPPED->value;
    }

    public function canDelete(): bool
    {
        $payment = $this->loadMissing('order.payment')->order->payment;

    }
}
