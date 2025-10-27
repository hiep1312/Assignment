<?php

namespace App\Enums\MailPlaceholders;

enum OrderSuccessPlaceholder: string
{
    case ORDER_CODE = '{{order_code}}';
    case CUSTOMER_NAME = '{{customer_name}}';
    case TOTAL_AMOUNT = '{{total_amount}}';
    case SHIPPING_FEE = '{{shipping_fee}}';
    case ORDER_ITEMS = '{{order_items}}';
    case SHIPPING_ADDRESS = '{{shipping_address}}';
    case PAYMENT_METHOD = '{{payment_method}}';
    case ORDER_DATE = '{{order_date}}';

    public function description(): string
    {
        return match($this){
            self::ORDER_CODE => 'Order code',
            self::CUSTOMER_NAME => 'Customer name',
            self::TOTAL_AMOUNT => 'Total order amount',
            self::SHIPPING_FEE => 'Shipping fee',
            self::ORDER_ITEMS => 'List of ordered products',
            self::SHIPPING_ADDRESS => 'Shipping address',
            self::PAYMENT_METHOD => 'Payment method',
            self::ORDER_DATE => 'Order creation date',
        };
    }
}
