<?php

namespace App\Enums\NotificationPlaceholders;

enum OrderUpdatePlaceholder: string
{
    case FIRST_NAME = '{{first_name}}';
    case LAST_NAME = '{{last_name}}';
    case NAME = '{{name}}';
    case EMAIL = '{{email}}';
    case ORDER_CODE = '{{order_code}}';
    case ORDER_STATUS = '{{order_status}}';
    case TOTAL_AMOUNT = '{{total_amount}}';
    case SHIPPING_FEE = '{{shipping_fee}}';
    case STATUS_AT = '{{status_at}}';
    case CANCEL_REASON = '{{cancel_reason}}';
    case RECIPIENT_NAME = '{{recipient_name}}';
    case PHONE = '{{phone}}';
    case SHIPPING_ADDRESS = '{{shipping_address}}';
    case PROVINCE = '{{province}}';
    case DISTRICT = '{{district}}';
    case WARD = '{{ward}}';
    case STREET = '{{street}}';

    public function description(): string
    {
        return match ($this) {
            self::FIRST_NAME => 'User first name',
            self::LAST_NAME => 'User last name',
            self::NAME => 'User full name',
            self::EMAIL => 'User email address',
            self::ORDER_CODE => 'Unique order code',
            self::ORDER_STATUS => 'Current order status (New, Confirmed, Processing, Shipped, Delivered, etc.)',
            self::TOTAL_AMOUNT => 'Total order amount (formatted)',
            self::SHIPPING_FEE => 'Shipping fee (formatted)',
            self::STATUS_AT => 'Datetime corresponding to the current order status (e.g., order placed, delivered, etc.)',
            self::CANCEL_REASON => 'Reason for cancellation (if applicable)',
            self::RECIPIENT_NAME => 'Name of the person receiving the order',
            self::PHONE => 'Recipient phone number',
            self::SHIPPING_ADDRESS => 'Full shipping address',
            self::PROVINCE => 'Province or city name',
            self::DISTRICT => 'District name',
            self::WARD => 'Ward or commune name',
            self::STREET => 'Street name or house number',
        };
    }
}
