<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_code',
        'total_amount',
        'shipping_fee',
        'status',
        'customer_note',
        'admin_note',
        'cancel_reason',
        'confirmed_at',
        'processing_at',
        'shipped_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'shipping_fee' => 'integer',
        'status' => 'integer',
        'confirmed_at' => 'datetime',
        'processing_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function shipping()
    {
        return $this->hasOne(OrderShipping::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function getIsFinalAttribute(): bool
    {
        return ($this->status === OrderStatus::DELIVERED->value) && !($this->completed_at || $this->cancelled_at);
    }

    public function getIsFinalizedAttribute(): bool
    {
        return (
            ($this->status === OrderStatus::COMPLETED->value && $this->completed_at) ||
            ($this->status === OrderStatus::FAILED->value && $this->cancelled_at)
        );
    }

    public function getIsCancelledAttribute(): bool
    {
        return ($this->status === OrderStatus::BUYER_CANCEL->value || $this->status === OrderStatus::ADMIN_CANCEL->value);
    }

    public function allowCancel(): bool
    {
        return in_array($this->status, [OrderStatus::NEW->value, OrderStatus::CONFIRMED->value, OrderStatus::PROCESSING->value]) && !$this->shipped_at;
    }

    public function allowAdminNote(): bool
    {
        return $this->allowCancel();
    }
}
