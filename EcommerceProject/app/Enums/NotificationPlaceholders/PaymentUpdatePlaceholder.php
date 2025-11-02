<?php

namespace App\Enums\NotificationPlaceholders;

enum PaymentUpdatePlaceholder: string
{
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case EMAIL = '{{email}}';
    case ORDER_CODE = '{{order_code}}';
    case PAYMENT_METHOD = '{{payment_method}}';
    case PAYMENT_STATUS = '{{payment_status}}';
    case AMOUNT = '{{amount}}';
    case TRANSACTION_ID = '{{transaction_id}}';
    case PAID_AT = '{{paid_at}}';

    public function description(): string
    {
        return match ($this) {
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'User full name',
            self::EMAIL => 'User email address',
            self::ORDER_CODE => 'Unique order code associated with the payment',
            self::PAYMENT_METHOD => 'Payment method used (Cash, Bank Transfer, Credit Card, etc.)',
            self::PAYMENT_STATUS => 'Current payment status (Pending, Paid, Failed)',
            self::AMOUNT => 'Total payment amount (formatted)',
            self::TRANSACTION_ID => 'Unique transaction identifier provided by the payment gateway',
            self::PAID_AT => 'Date and time when the payment was successfully completed',
        };
    }
}
