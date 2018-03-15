<?php

namespace Maxfactor\Checkout\Handlers;

use Omnipay\Omnipay;

class Stripe
{
    protected $gateway;
    protected $token;
    protected $currency = 'GBP';
    protected $amount;
    protected $reference;

    /**
     * Create the payment gateway
     *
     * @param string $gateway
     */
    public function __construct(string $gateway = 'Stripe')
    {
        $this->gateway = Omnipay::create($gateway)->setApiKey(env('STRIPE_API_KEY'));
    }

    /**
     * For stripe, pass in the client-side generated payment token
     *
     * @param string $token
     * @return void
     */
    public function token(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Set the currency to be used for this payment
     *
     * @param string $currency
     * @return void
     */
    public function currency(string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Set the amount to charge to the card (Must be a float or a string)
     *
     * @param string|float $amount
     * @return void
     */
    public function amount($amount)
    {
        $this->amount = round($amount, 2);

        return $this;
    }

    /**
     * Optional: Supply an order ID/Reference. This will be passed to the payment
     * provider.
     *
     * @param string $reference
     * @return void
     */
    public function reference(string $reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Submit the payment request and return the result
     *
     * @return void
     */
    public function charge()
    {
        return $this->gateway->purchase([
            'amount' => $this->amount,
            'currency' => $this->currency,
            'token' => $this->token,
            'metadata' => [
                'orderID' => $this->reference,
            ]
        ])->send();
    }
}
