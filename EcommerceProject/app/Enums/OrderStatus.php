<?php

namespace App\Enums;

enum OrderStatus: int
{
    case NEW = 1;
    case CONFIRMED = 2;
    case PROCESSING = 3;
    case SHIPPED = 4;
    case DELIVERED = 5;
    case COMPLETED = 6;
    case FAILED = 7;
    case BUYER_CANCEL = 8;
    case ADMIN_CANCEL = 9;

    public function timestampColumn(): ?string
    {
        return match($this){
            self::NEW => 'created_at',
            self::CONFIRMED => 'confirmed_at',
            self::PROCESSING => 'processing_at',
            self::SHIPPED => 'shipped_at',
            self::DELIVERED => 'delivered_at',
            self::COMPLETED => 'completed_at',
            self::FAILED, self::BUYER_CANCEL, self::ADMIN_CANCEL => 'cancelled_at',
        };
    }
}
