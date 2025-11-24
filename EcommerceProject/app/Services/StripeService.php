<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Log;
use Exception;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret', ''));
    }

    public function createCheckoutSession(array $data)
    {
        try {
            $user = User::find($data['user_id']);
            $address = $data['address_id'] ? UserAddress::find($data['address_id']) : null;

            $customerData = $this->prepareCustomerData($user, $address);

            $customerId = $this->getOrCreateCustomer($customerData);

            $sessionParams = [
                'automatic_tax' => [
                    'enabled' => false
                ],
                'ui_mode' => 'embedded',
                'mode' => $data['mode'] ?? 'payment',
                'customer' => $customerId,
                'customer_email' => $user->email,
                'client_reference_id' => $data['client_reference_id'] ?? '',
                'locale' => $data['locale'] ?? 'auto',
                'currency' => $data['currency'] ?? null,

                // Line items
                'line_items' => $this->prepareLineItems($data['items']),

                // URLs
                'return_url' => $data['return_url'],
                'success_url' => $data['success_url'] ?? null,
                'cancel_url' => $data['cancel_url'] ?? null,

                // Metadata
                'metadata' => array_merge([
                    'user_id' => $user->id,
                    'order_id' => $data['order_id'] ?? null,
                ], $data['metadata'] ?? []),

                // Tax settings
                'automatic_tax' => [
                    'enabled' => $data['automatic_tax_enabled'] ?? false,
                ],

                // Billing
                'billing_address_collection' => $data['billing_address_collection'] ?? 'auto',

                // Customer settings
                'customer_creation' => $data['customer_creation'] ?? 'if_required',
                'customer_update' => [
                    'address' => $data['customer_update_address'] ?? 'never',
                    'name' => $data['customer_update_name'] ?? 'auto',
                    'shipping' => $data['customer_update_shipping'] ?? 'never',
                ],

                // Payment settings
                'payment_method_types' => $data['payment_method_types'] ?? null,
                'allow_promotion_codes' => $data['allow_promotion_codes'] ?? false,

                // Redirect settings
                'redirect_on_completion' => $data['redirect_on_completion'] ?? 'always',

                // Submit type
                'submit_type' => $data['submit_type'] ?? 'auto',

                // Saved payment methods
                'saved_payment_method_options' => [
                    'allow_redisplay_filters' => [$data['allow_redisplay_filters'] ?? 'always'],
                    'payment_method_remove' => $data['payment_method_remove'] ?? 'enabled',
                    'payment_method_save' => $data['payment_method_save'] ?? 'disabled',
                ],

                // Origin
                'origin_context' => $data['origin_context'] ?? 'web',
            ];

            // Thêm expires_at nếu có
            if (!empty($data['expires_at'])) {
                $sessionParams['expires_at'] = $data['expires_at'];
            }

            // Thêm after_expiration nếu có
            if (!empty($data['after_expiration'])) {
                $sessionParams['after_expiration'] = [
                    'recovery' => [
                        'enabled' => $data['after_expiration']['recovery']['enabled'] ?? false,
                        'allow_promotion_codes' => $data['after_expiration']['recovery']['allow_promotion_codes'] ?? false,
                    ],
                ];
            }

            // Thêm adaptive_pricing nếu có
            if (!empty($data['adaptive_pricing'])) {
                $sessionParams['adaptive_pricing'] = [
                    'enabled' => $data['adaptive_pricing']['enabled'] ?? false,
                ];
            }

            // Thêm branding_settings nếu có
            if (!empty($data['branding_settings'])) {
                $sessionParams = array_merge($sessionParams,
                    $this->prepareBrandingSettings($data['branding_settings'])
                );
            }

            // Thêm custom_fields nếu có
            if (!empty($data['custom_fields'])) {
                $sessionParams['custom_fields'] = $data['custom_fields'];
            }

            // Thêm custom_text nếu có
            if (!empty($data['custom_text'])) {
                $sessionParams['custom_text'] = $data['custom_text'];
            }

            // Thêm invoice_creation nếu có
            if (!empty($data['invoice_creation'])) {
                $sessionParams['invoice_creation'] = $this->prepareInvoiceCreation($data['invoice_creation']);
            }

            // Thêm shipping_options nếu có
            if (!empty($data['shipping_options'])) {
                $sessionParams['shipping_options'] = $this->prepareShippingOptions($data['shipping_options']);
            }

            // Thêm name collection settings
            if (!empty($data['name_collection'])) {
                $sessionParams['name_collection'] = $data['name_collection'];
            }

            // Tạo session
            $session = Session::create(array_filter($sessionParams));

            return [
                'success' => true,
                'session_id' => $session->id,
                'client_secret' => $session->client_secret,
                'url' => $session->url,
                'session' => $session,
            ];

        } catch (Exception $e) {
            Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function retrieveSession(string $sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);
            return [
                'success' => true,
                'session' => $session,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Retrieve Session Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function getOrCreateCustomer(array $customerData)
    {
        try {
            // Tìm customer theo email
            $customers = Customer::all(['email' => $customerData['email'], 'limit' => 1]);

            if (count($customers->data) > 0) {
                return $customers->data[0]->id;
            }

            // Tạo customer mới
            $customer = Customer::create($customerData);
            return $customer->id;

        } catch (Exception $e) {
            Log::error('Stripe Customer Error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function prepareCustomerData(User $user, ?UserAddress $address): array
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'description' => "Customer ID: {$user->id}",
            'metadata' => [
                'user_id' => $user->id,
                'username' => $user->username,
            ]
        ];

        if ($address) {
            $data['phone'] = $address->phone;
            $data['shipping'] = [
                'name' => $address->recipient_name,
                'phone' => $address->phone,
                'address' => [
                    'country' => 'VN',
                    'city' => $address->district,
                    'line1' => $address->ward,
                    'line2'=> $address->street ?? '',
                    'state' => $address->province,
                    'postal_code' => $address->postal_code ?? '',
                ],
            ];
        }

        return $data;
    }

    protected function prepareLineItems(array $items): array
    {
        $lineItems = [];

        foreach ($items as $item) {
            $lineItem = [
                'quantity' => $item['quantity'] ?? 1,
            ];

            if (!empty($item['adjustable_quantity'])) {
                $lineItem['adjustable_quantity'] = [
                    'enabled' => $item['adjustable_quantity']['enabled'] ?? false,
                    'minimum' => $item['adjustable_quantity']['minimum'] ?? 1,
                    'maximum' => $item['adjustable_quantity']['maximum'] ?? 99,
                ];
            }

            if (!empty($item['price_data'])) {
                $lineItem['price_data'] = [
                    'currency' => strtolower($item['price_data']['currency'] ?? 'vnd'),
                    'unit_amount' => $item['price_data']['unit_amount'],
                    'product_data' => [
                        'name' => $item['price_data']['product_data']['name'],
                        'description' => $item['price_data']['product_data']['description'] ?? null,
                        'images' => $item['price_data']['product_data']['images'] ?? [],
                    ],
                ];

                if (!empty($item['price_data']['product_data']['unit_label'])) {
                    $lineItem['price_data']['product_data']['unit_label'] =
                        $item['price_data']['product_data']['unit_label'];
                }

                if (!empty($item['price_data']['tax_behavior'])) {
                    $lineItem['price_data']['tax_behavior'] = $item['price_data']['tax_behavior'];
                }
            } else if (!empty($item['price'])) {
                $lineItem['price'] = $item['price'];
            }

            $lineItems[] = array_filter($lineItem);
        }

        return $lineItems;
    }

    protected function prepareBrandingSettings(array $settings)
    {
        $branding = [];

        if (!empty($settings['background_color'])) {
            $branding['background_color'] = $settings['background_color'];
        }

        if (!empty($settings['button_color'])) {
            $branding['button_color'] = $settings['button_color'];
        }

        if (!empty($settings['display_name'])) {
            $branding['display_name'] = $settings['display_name'];
        }

        if (!empty($settings['font_family'])) {
            $branding['font_family'] = $settings['font_family'];
        }

        if (!empty($settings['border_style'])) {
            $branding['border_style'] = $settings['border_style'];
        }

        // Icon
        if (!empty($settings['icon'])) {
            if ($settings['icon']['type'] === 'url' && !empty($settings['icon']['url'])) {
                $branding['icon'] = $settings['icon']['url'];
            } else if ($settings['icon']['type'] === 'file' && !empty($settings['icon']['file'])) {
                $branding['icon'] = $settings['icon']['file'];
            }
        }

        // Logo
        if (!empty($settings['logo'])) {
            if ($settings['logo']['type'] === 'url' && !empty($settings['logo']['url'])) {
                $branding['logo'] = $settings['logo']['url'];
            } else if ($settings['logo']['type'] === 'file' && !empty($settings['logo']['file'])) {
                $branding['logo'] = $settings['logo']['file'];
            }
        }

        return $branding;
    }

    protected function prepareInvoiceCreation(array $invoiceData)
    {
        $invoice = [
            'enabled' => $invoiceData['enabled'] ?? true,
        ];

        if (!empty($invoiceData['invoice_data'])) {
            $invoice['invoice_data'] = [];

            if (!empty($invoiceData['invoice_data']['custom_fields'])) {
                $invoice['invoice_data']['custom_fields'] = $invoiceData['invoice_data']['custom_fields'];
            }

            if (!empty($invoiceData['invoice_data']['description'])) {
                $invoice['invoice_data']['description'] = $invoiceData['invoice_data']['description'];
            }

            if (!empty($invoiceData['invoice_data']['footer'])) {
                $invoice['invoice_data']['footer'] = $invoiceData['invoice_data']['footer'];
            }

            if (!empty($invoiceData['invoice_data']['rendering_options'])) {
                $invoice['invoice_data']['rendering_options'] = $invoiceData['invoice_data']['rendering_options'];
            }
        }

        return $invoice;
    }

    protected function prepareShippingOptions(array $shippingOptions)
    {
        $options = [];

        foreach ($shippingOptions as $option) {
            $shippingOption = [];

            if (!empty($option['shipping_rate_data'])) {
                $rateData = [
                    'display_name' => $option['shipping_rate_data']['display_name'],
                    'type' => $option['shipping_rate_data']['type'] ?? 'fixed_amount',
                ];

                // Fixed amount
                if (!empty($option['shipping_rate_data']['fixed_amount'])) {
                    $rateData['fixed_amount'] = [
                        'amount' => $option['shipping_rate_data']['fixed_amount']['amount'],
                        'currency' => strtolower($option['shipping_rate_data']['fixed_amount']['currency'] ?? 'vnd'),
                    ];
                }

                // Delivery estimate
                if (!empty($option['shipping_rate_data']['delivery_estimate'])) {
                    $rateData['delivery_estimate'] = [];

                    if (!empty($option['shipping_rate_data']['delivery_estimate']['minimum'])) {
                        $rateData['delivery_estimate']['minimum'] = $option['shipping_rate_data']['delivery_estimate']['minimum'];
                    }

                    if (!empty($option['shipping_rate_data']['delivery_estimate']['maximum'])) {
                        $rateData['delivery_estimate']['maximum'] = $option['shipping_rate_data']['delivery_estimate']['maximum'];
                    }
                }

                $shippingOption['shipping_rate_data'] = $rateData;
            } else if (!empty($option['shipping_rate'])) {
                $shippingOption['shipping_rate'] = $option['shipping_rate'];
            }

            $options[] = $shippingOption;
        }

        return $options;
    }

    public function handleWebhook(string $payload, string $signature)
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;

                case 'checkout.session.expired':
                    $this->handleCheckoutSessionExpired($event->data->object);
                    break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                default:
                    Log::info('Unhandled webhook event: ' . $event->type);
            }

            return ['success' => true];

        } catch (Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info('Checkout Session Completed', [
            'session_id' => $session->id,
            'customer' => $session->customer,
            'metadata' => $session->metadata,
        ]);
    }

    protected function handleCheckoutSessionExpired($session)
    {
        Log::info('Checkout Session Expired', [
            'session_id' => $session->id,
            'metadata' => $session->metadata,
        ]);

        // Xử lý khi session hết hạn
    }

    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment Intent Succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
        ]);

        // Xử lý payment thành công
    }

    /**
     * Xử lý khi payment intent thất bại
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        Log::error('Payment Intent Failed', [
            'payment_intent_id' => $paymentIntent->id,
            'error' => $paymentIntent->last_payment_error,
        ]);

        // Xử lý payment thất bại
    }

    /**
     * Lấy thông tin payment intent
     */
    public function retrievePaymentIntent(string $paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Retrieve Payment Intent Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Huỷ payment intent
     */
    public function cancelPaymentIntent(string $paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent->cancel();

            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Cancel Payment Intent Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Lấy danh sách sessions
     */
    public function listSessions(array $params = [])
    {
        try {
            $sessions = Session::all($params);
            return [
                'success' => true,
                'sessions' => $sessions,
            ];
        } catch (Exception $e) {
            Log::error('Stripe List Sessions Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Expire một session
     */
    public function expireSession(string $sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);
            $session->expire();

            return [
                'success' => true,
                'session' => $session,
            ];
        } catch (Exception $e) {
            Log::error('Stripe Expire Session Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
