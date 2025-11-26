<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipping;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Throwable;
use App\Enums\PaymentMethod;
use InvalidArgumentException;
use Stripe\Event;
use Stripe\StripeObject;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret', ''));
    }

    protected function defaultReturnUrl(): string
    {
        return '';
    }

    protected function getEventHandler(string $event): callable
    {
        return match(true){
            Event::CHECKOUT_SESSION_COMPLETED => fn(StripeObject $data) => null,
            Event::CHECKOUT_SESSION_EXPIRED => fn(StripeObject $data) => null,
            Event::PAYMENT_INTENT_SUCCEEDED => fn(StripeObject $data) => null,
            Event::PAYMENT_INTENT_PAYMENT_FAILED => fn(StripeObject $data) => null,
            Event::PAYMENT_INTENT_CANCELED => fn(StripeObject $data) => null,
            default => fn() => Log::info("Unhandled webhook event: {$event}")
        };
    }

    public function createCheckoutSession(Order|array $order, ?string $returnUrl = null, bool $isStripePayment = false): array
    {
        try {
            if($order instanceof Order) {
                $order->loadMissing('user', 'items.productVariant.product', 'items.productVariant.inventory', 'shipping', 'payment');
            }

            $customerObject = null;
            if(!empty($order['user'])) {
                $user = $order['user'];
                $shippingAddress = $order['shipping'] ?? null;

                $customerData = $this->prepareCustomerData($user, $shippingAddress);
                $customerObject = $this->getOrCreateCustomer($customerData);
            }

            $sessionOptions = [
                'ui_mode' => 'embedded',
                'mode' => 'payment',

                'client_reference_id' => $order['order_code'],
                'locale' => 'auto',
                'return_url' => $returnUrl ?? $this->defaultReturnUrl(),

                'customer' => $customerObject?->id ?? '',
                'customer_email' => $customerObject?->email ?? '',

                'metadata' => [
                    'user_id' => $order['user_id'] ?? 'N/A',
                    'order_id' => $order['id'] ?? 'N/A',
                ],

                'customer_creation' => 'if_required',
                'customer_update' => [
                    'address' => 'never',
                    'name' => 'auto',
                    'shipping' => 'never',
                ],

                'expires_at' => strtotime('+2 hours', strtotime($order['payment']['created_at'] ?? $order['created_at'])),
                'invoice_creation' => $this->prepareInvoiceCreation($order['order_code']),
                'shipping_options' => $this->prepareShippingOptions($order),
                'branding_settings' => $this->prepareBrandingSettings(),
                'allow_promotion_codes' => false,
                'billing_address_collection' => 'auto',
                'currency' => 'VND',
                'origin_context' => 'web',
                'redirect_on_completion' => 'always',
                'submit_type' => 'pay',
                'livemode' => false,

                'automatic_tax' => [
                    'enabled' => false
                ],

                'adaptive_pricing' => [
                    'enabled' => false
                ],

                'after_expiration' => [
                    'recovery' => [
                        'enabled' => false,
                        'allow_promotion_codes' => false
                    ]
                ],

                'name_collection' => [
                    'business' => [
                        'enabled' => false,
                        'optional' => false
                    ],

                    'individual' => [
                        'enabled' => false,
                        'optional' => false
                    ]
                ],

                'saved_payment_method_options' => [
                    'allow_redisplay_filters' => ['always'],
                    'payment_method_remove' => 'enabled',
                    'payment_method_save' => 'disabled',
                ],
            ];

            if(!empty($order['items'])) {
                $sessionOptions['line_items'] = $this->prepareLineItems($order['items']);
            }

            if($isStripePayment && !empty($order['payment']['method'])) {
                $paymentMethod = ($order['payment']['method'] instanceof PaymentMethod)
                    ? $order['payment']['method']
                    : PaymentMethod::tryFrom($order['payment']['method']);

                $stripePaymentType = match($paymentMethod) {
                    PaymentMethod::BANK_TRANSFER => 'bank_transfer',
                    PaymentMethod::CREDIT_CARD => 'card',
                    default => throw new InvalidArgumentException("Unsupported payment method: {$paymentMethod?->value}"),
                };

                $sessionOptions['payment_method_types'] = [$stripePaymentType];
            }

            $session = Session::create($sessionOptions);

            return [
                'success' => true,
                'session_id' => $session->id,
                'client_reference_id' => $session->client_reference_id,
                'metadata' => $session->metadata,
                'client_secret' => $session->client_secret,
                'created_at' => $session->created,
                'payment_method_types' => $session->payment_method_types
            ];

        } catch(Throwable $error) {
            Log::error("Stripe Checkout Session Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage(),
            ];
        }
    }

    public function updateSession(string $sessionId, array $data): array
    {
        try {
            $session = Session::update($sessionId, $data);

            return [
                'success' => true,
                ...$session->toArray(),
            ];

        }catch(Throwable $error) {
            Log::error("Stripe Update Session Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage(),
            ];
        }
    }

    public function retrieveSession(string $sessionId): array
    {
        try {
            $session = Session::retrieve($sessionId);

            return [
                'success' => true,
                ...$session->toArray(),
            ];
        }catch(Throwable $error) {
            Log::error("Stripe Retrieve Session Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage(),
            ];
        }
    }

    public function listSessions(array $params = []): array
    {
        try {
            $sessions = Session::all($params);

            return [
                'success' => true,
                ...$sessions,
            ];
        }catch(Throwable $error) {
            Log::error("Stripe List Sessions Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage(),
            ];
        }
    }

    public function expireSession(string $sessionId): array
    {
        try {
            $session = Session::retrieve($sessionId);
            $session->expire();

            return [
                'success' => true,
                ...$session->toArray(),
            ];

        }catch(Throwable $error) {
            Log::error("Stripe Expire Session Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage(),
            ];
        }
    }

    public function getOrCreateCustomer(array $customerData): ?object
    {
        try {
            $customers = Customer::all([
                'email' => $customerData['email'],
                'limit' => 1
            ]);

            if (!$customers->isEmpty()) {
                return $customers->first();
            }

            $customer = Customer::create($customerData);
            return $customer;
        }catch(Throwable $error) {
            Log::error("Stripe Customer Error: {$error}");

            return null;
        }
    }

    protected function prepareCustomerData(User|array $user, OrderShipping|array|null $address): array
    {
        $data = [
            'name' => $user['name'],
            'email' => $user['email'],
            'description' => "Customer ID: {$user['id']}",
            'metadata' => [
                'user_id' => $user['id'],
                'username' => $user['username'],
            ]
        ];

        if ($address) {
            $data['phone'] = $address['phone'];
            $data['shipping'] = [
                'name' => $address['recipient_name'],
                'phone' => $address['phone'],
                'address' => [
                    'country' => 'VN',
                    'state' => $address['province'],
                    'city' => $address['district'],
                    'line1' => $address['ward'],
                    'line2'=> $address['street'] ?? '',
                    'postal_code' => $address['postal_code'] ?? '',
                ],
            ];
        }

        return $data;
    }

    protected function prepareLineItems(Collection|array $items): array
    {
        $lineItems = [];

        foreach($items as $item) {
            if(!($item instanceof OrderItem || is_array($item))) continue;

            $lineItem = [
                'quantity' => $item['quantity'] ?? 1
            ];

            if(!empty($item['productVariant'])) {
                $variant = $item['productVariant'];
                $lineItem['price_data'] = [
                    'currency' => 'VND',
                    'tax_behavior' => 'unspecified',
                    'unit_amount' => $variant['discount'] ?? $variant['price']
                ];

                if(!empty($variant['inventory'])) {
                    $inventory = $variant['inventory'];
                    $lineItem['adjustable_quantity'] = [
                        'enabled' => false,
                        'maximum' => $inventory['stock'],
                        'minimum' => 1
                    ];
                }

                if(!empty($variant['product'])) {
                    $product = $variant['product'];
                    $lineItem['price_data']['product_data'] = [
                        'name' => $product['title'] . ' - ' . $variant['name'],
                        'description' => $product['description'],
                        'metadata' => [
                            'product_id' => $product['id'],
                            'variant_id' => $variant['id'],
                        ],
                        'unit_label' => 'volume'
                    ];

                    if(!empty($product['images'])) {
                        $images = $product['images'];
                        $lineItem['price_data']['product_data']['images'] = array_map(
                            fn($image) => $image['image_url'],
                            ($images instanceof Image) ? $images->toArray() : $images
                        );
                    }
                }
            }

            $lineItems[] = $lineItem;
        }

        return $lineItems;
    }

    protected function prepareBrandingSettings(): array
    {
        $defaultBranding = [
            'background_color' => '#FFF9E6',
            'border_style' => 'rounded',
            'button_color' => '#F59E0B',
            'display_name' => 'Bookio Payment',
            'font_family' => 'be_vietnam_pro',
            'icon' => [
                'type' => 'url',
                'url' => asset('storage/logo-bookio.ico')
            ],
            'logo' => [
                'type' => 'url',
                'url' => asset('storage/logo-bookio.webp')
            ]
        ];

        return $defaultBranding;
    }

    protected function prepareInvoiceCreation(string $orderCode): array
    {
        $invoice = [
            'enabled' => true,
            'invoice_data' => [
                'custom_fields' => [
                    [
                        'name' => 'Order Code',
                        'value' => $orderCode
                    ]
                ],
                'description' => 'Book purchase from Bookio - Your trusted online bookstore.',
                'footer' => 'Thank you for shopping at Bookio! Payment is due within 15 days. Questions? Contact us at support@bookio.com.',
                'rendering_options' => [
                    'amount_tax_display' => 'exclude_tax'
                ]
            ]
        ];

        return $invoice;
    }

    protected function prepareShippingOptions(Order|array $orderData): array
    {
        $options = [
            'shipping_rate_data' => [
                'display_name' => 'Standard Shipping',
                'delivery_estimate' => [
                    'maximum' => [
                        'unit' => 'week',
                        'value' => 2
                    ],

                    'minimum' => [
                        'unit' => 'day',
                        'value' => 1
                    ]
                ],

                'fixed_amount' => [
                    'amount' => $orderData['shipping_fee'] * 100,
                    'currency' => 'VND'
                ],

                'tax_behavior' => 'unspecified',
                'type' => 'fixed_amount'
            ]
        ];

        return $options;
    }

    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                ...$paymentIntent->toArray(),
            ];

        }catch(Throwable $error) {
            Log::error("Stripe Retrieve Payment Intent Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage(),
            ];
        }
    }

    public function cancelPaymentIntent(string $paymentIntentId, string $cancellationReason = ''): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent->cancel(['cancellation_reason' => $cancellationReason]);

            return [
                'success' => true,
                ...$paymentIntent->toArray(),
            ];

        }catch(Throwable $error) {
            Log::error("Stripe Cancel Payment Intent Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage(),
            ];
        }
    }

    public function handleWebhook(?string $payload = null, ?string $signature = null): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload ?? @file_get_contents('php://input'),
                $signature ?? $_SERVER['HTTP_STRIPE_SIGNATURE'],
                config('services.stripe.webhook_secret', ''),
                Webhook::DEFAULT_TOLERANCE
            );

            $handler = $this->getEventHandler($event->type);
            $handler($event->data);

            return [
                'success' => true,
                'event' => $event->type,
                ...$event->data->toArray()
            ];

        }catch(Throwable $error) {
            Log::error("Stripe Webhook Error: {$error}");

            return [
                'success' => false,
                'error' => $error->getMessage()
            ];
        }
    }
}
