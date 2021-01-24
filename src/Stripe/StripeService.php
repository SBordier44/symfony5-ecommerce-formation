<?php

namespace App\Stripe;

use App\Entity\Purchase;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeService
{
    public function __construct(
        protected string $privateKey,
        protected string $publicKey
    ) {
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPaymentIntent(Purchase $purchase): PaymentIntent
    {
        Stripe::setApiKey($this->privateKey);

        return PaymentIntent::create(
            [
                'amount' => $purchase->getTotal(),
                'currency' => 'eur'
            ]
        );
    }
}
