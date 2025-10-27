<?php

namespace App\Enums\MailPlaceholders;

enum ShippingUpdatePlaceholder: string
{
    case ORDER_CODE = '{{order_code}}';
    case CUSTOMER_NAME = '{{customer_name}}';
    case STATUS = '{{status}}';
    case ESTIMATED_DELIVERY = '{{estimated_delivery}}';

    public function description(): string
    {
        return match ($this) {
            self::ORDER_CODE => 'Order code',
            self::CUSTOMER_NAME => 'Customer name',
            self::STATUS => 'Current shipping status (processing/shipped/delivered)',
            self::ESTIMATED_DELIVERY => 'Estimated delivery time',
        };
    }
}
