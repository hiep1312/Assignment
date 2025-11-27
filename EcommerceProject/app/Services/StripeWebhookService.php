<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\StripeObject;

class StripeWebhookService
{
    public static function handleCheckoutSessionCompleted(StripeObject $session): void
    {
        Log::debug('Stripe Payment Intent Succeeded', ['intent'=> $session]);
    }

    public static function handleCheckoutSessionExpired(StripeObject $session): void
    {
        Log::debug('Stripe Payment Intent Succeeded', ['intent'=> $session]);
    }

    public static function handlePaymentIntentSucceeded(StripeObject $intent): void
    {
        Log::debug('Stripe Payment Intent Succeeded', ['intent'=> $intent]);
    }

    public static function handlePaymentIntentFailed(StripeObject $intent): void
    {
        Log::debug('Stripe Payment Intent Succeeded', ['intent'=> $intent]);
    }

    public static function handlePaymentIntentCanceled(StripeObject $intent): void
    {
        Log::debug('Stripe Payment Intent Succeeded', ['intent'=> $intent]);
    }
}
