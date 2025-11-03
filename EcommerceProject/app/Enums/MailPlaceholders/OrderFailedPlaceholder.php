<?php

namespace App\Enums\MailPlaceholders;

use App\Enums\DefaultImage;
use App\Models\Order;
use Illuminate\Mail\Message;
use Illuminate\Support\Arr;

enum OrderFailedPlaceholder: string
{
    case ORDER_CODE = '{{order_code}}';
    case CUSTOMER_NAME = '{{customer_name}}';
    case CUSTOMER_EMAIL = '{{customer_email}}';
    case CUSTOMER_AVATAR = '{{customer_avatar}}';
    case TOTAL_AMOUNT = '{{total_amount}}';
    case SHIPPING_FEE = '{{shipping_fee}}';
    case ORDER_ITEMS = '{{order_items}}';
    case SHIPPING_ADDRESS = '{{shipping_address}}';
    case PAYMENT_METHOD = '{{payment_method}}';
    case CANCEL_REASON = '{{cancel_reason}}';
    case FAILED_DATE = '{{failed_date}}';

    public function description(): string
    {
        return match ($this) {
            self::ORDER_CODE => 'Order code',
            self::CUSTOMER_NAME => 'Customer name',
            self::CUSTOMER_EMAIL => 'Customer email address',
            self::CUSTOMER_AVATAR => 'URL or path to the customer avatar image',
            self::TOTAL_AMOUNT => 'Total order amount',
            self::SHIPPING_FEE => 'Shipping fee',
            self::ORDER_ITEMS => 'List of ordered products',
            self::SHIPPING_ADDRESS => 'Shipping address',
            self::PAYMENT_METHOD => 'Payment method',
            self::CANCEL_REASON => 'Reason for order cancellation',
            self::FAILED_DATE => 'Date of order failure',
        };
    }

    protected static function resolveValues(Order $order, Message $message): array
    {
        return [
            self::ORDER_CODE->value => fn() => $order->order_code,
            self::CUSTOMER_NAME->value => fn() => $order->user->name,
            self::CUSTOMER_EMAIL->value => fn() => $order->user->email,
            self::CUSTOMER_AVATAR->value => function() use ($order, $message){
                $avatarCid = $message->embed(public_path("storage/" . ($order->user->avatar ?? DefaultImage::AVATAR->value)));
                return <<<HTML
                    <div style="flex-shrink: 0;">
                        <img src="{$avatarCid}" alt="" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid #667eea;">
                    </div>
                HTML;
            },
            self::TOTAL_AMOUNT->value => fn() => number_format($order->total_amount, 0, '.', '.') . 'đ',
            self::SHIPPING_FEE->value => fn() => number_format($order->shipping_fee, 0, '.', '.') . 'đ',
            self::ORDER_ITEMS->value => function() use ($order){
                $rowsHtml = implode('', array_map(function($orderItem){
                    $priceFormatted = number_format($orderItem->price, 0, '.', '.');
                    return <<<HTML
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 16px 0; font-size: 14px; color: #333;">
                                <div style="font-weight: 600;">{$orderItem->productVariant->product->title}</div>
                                <div style="font-size: 12px; color: #999; margin-top: 4px;">Variant: {$orderItem->productVariant->name}</div>
                            </td>
                            <td style="padding: 16px 0; text-align: center; font-size: 14px; color: #666; font-weight: 500;">{$orderItem->quantity}</td>
                            <td style="padding: 16px 0; text-align: right; font-size: 14px; color: #333; font-weight: 600;">{$priceFormatted}đ</td>
                        </tr>
                    HTML;
                }, $order->items->all()));

                return <<<HTML
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #667eea;">
                                <th style="text-align: left; padding: 12px 0; font-size: 13px; color: #667eea; font-weight: 600; text-transform: uppercase;">Product</th>
                                <th style="text-align: center; padding: 12px 0; font-size: 13px; color: #667eea; font-weight: 600; text-transform: uppercase;">Quantity</th>
                                <th style="text-align: right; padding: 12px 0; font-size: 13px; color: #667eea; font-weight: 600; text-transform: uppercase;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$rowsHtml}
                        </tbody>
                    </table>
                HTML;
            },
            self::SHIPPING_ADDRESS->value => fn() => $order->shipping->address,
            self::PAYMENT_METHOD->value => fn() => ucwords(str_replace('_', ' ', $order->payment->method->value)),
            self::CANCEL_REASON->value => fn() => $order->cancel_reason,
            self::FAILED_DATE->value => fn() => $order->cancelled_at->format('m/d/Y H:i A')
        ];
    }

    public static function replacePlaceholders(string $template, Order $order, Message $message, ?array $variable = null): string
    {
        $order->loadMissing('user', 'shipping', 'payment', 'items.productVariant.product');
        $placeholders = Arr::only(self::resolveValues($order, $message), $variable ?? []);

        return str_ireplace(array_keys($placeholders), array_map(fn($resolver) => $resolver(), array_values($placeholders)), $template);
    }
}
