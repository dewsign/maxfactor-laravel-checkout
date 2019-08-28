<?php

namespace Maxfactor\Checkout\Handlers;

use Exception;
use Omnipay\Omnipay;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Stripe
{
    protected $gateway;
    protected $token;
    protected $currency = 'GBP';
    protected $amount;
    protected $reference;
    protected $idempotency;

    /**
     * Retrieve the payment token for the current checkout
     *
     * @return string
     */
    public static function getToken()
    {
        $token = Request::get('checkout')['payment']['paymentMethod']['id'];

        if (!$token) {
            $token = Session::get('paymentIntent')['token'];
        }

        if (!$token) {
            throw new Exception("The payment method token is missing", 404);
        }

        return $token;
    }

    /**
     * Create the payment gateway
     *
     * @param string $gateway
     */
    public function __construct(string $gateway = 'Stripe\PaymentIntents')
    {
        $this->gateway = Omnipay::create($gateway)->setApiKey(env('STRIPE_API_KEY'));
    }

    /**
     * Sets the Idempotency header in the stripe request to ensure thi stransaction can never be
     * processed more than once.
     *
     * @param string $key
     * @return void
     */
    public function idempotencyKey(string $key)
    {
        $this->idempotency = $key;

        return $this;
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
        $purchaseRequest = $this->gateway
            ->purchase([
                'amount' => $this->amount,
                'currency' => $this->currency,
                'paymentMethod' => $this->token,
                'confirm' => false,
                'metadata' => [
                    'orderID' => $this->reference,
                ]
            ]);

        /**
         * Ensure a transaction is only processed one.
         */
        if ($this->idempotency) {
            $purchaseRequest->setIdempotencyKeyHeader($this->idempotency);
        }

        /**
         * Send the request to Stripe
         */
        $purchase = $purchaseRequest->send();

        /**
         * Complete the transaction if the payment doesn't require 3D Secure verification
         */
        if (!$reference = $purchase->getPaymentIntentReference()) {
            return $purchase;
        }

        Session::put('paymentIntentReference', $reference);

        /**
         * The payment requires 3DS verification.
         */
        return $this->gateway->confirm([
            'paymentIntentReference' => $reference,
            'returnUrl' => route('checkout.show', [Route::current()->parameter('uid'), 'sca']),
        ])->send();
    }

    /**
     * Confirm the payment with the gateway after 3DS authentication.
     *
     * @param string $paymentIntentReference
     * @return void
     */
    public function confirm($paymentIntentReference)
    {
        return $this->gateway->confirm([
            'paymentIntentReference' => $paymentIntentReference,
        ])->send();
    }
}
