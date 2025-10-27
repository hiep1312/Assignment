<?php

namespace App\Enums\MailPlaceholders;

enum OrderFailedPlaceholder: string
{
    case ORDER_CODE = '{{order_code}}';
    case CUSTOMER_NAME = '{{customer_name}}';
    case CANCEL_REASON = '{{cancel_reason}}';
    case FAILED_DATE = '{{failed_date}}';

    public function description(): string
    {
        return match ($this) {
            self::ORDER_CODE => 'Order code',
            self::CUSTOMER_NAME => 'Customer name',
            self::CANCEL_REASON => 'Reason for order cancellation',
            self::FAILED_DATE => 'Date of order failure',
        };
    }
}
