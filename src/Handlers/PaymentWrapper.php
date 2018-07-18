<?php

namespace Maxfactor\Checkout\Handlers;

use Maxfactor\Checkout\Handlers\Paypal;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Maxfactor\Checkout\Handlers\Stripe;

class PaymentWrapper
{
    protected $uid;
    protected $amount;
    protected $orderID;
    protected $provider;

    /**
     * Create payment wrapper
     *
     * @param string $provider
     * @param string $total
     */
    public function __construct(string $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Set order UID
     *
     * @return PaymentWrapper
     */
    public function setUid(string $uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Set order billing amount
     *
     * @return PaymentWrapper
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set order ID
     *
     * @return PaymentWrapper
     */
    public function setOrderID(string $orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }

    /**
     * Delegate to relevant processing method
     *
     * @return array
     */
    public function process()
    {
        $paymentMethod = sprintf("process%s", ucfirst($this->provider));

        if (method_exists($this, $paymentMethod)) {
            return $this->{$paymentMethod}();
        }

        return [];
    }

    /**
     * Pass to stripe payment handler
     *
     * @return array
     */
    protected function processStripe()
    {
        $token = Request::get('checkout')['payment']['token']['id'];

        $paymentResponse = (new Stripe())
            ->idempotencyKey($token) // We only want to allow a single charge per token
            ->token($token)
            ->amount($this->amount)
            ->reference($this->orderID)
            ->charge();

        return $paymentResponse->getData();
    }

    /**
     * Pass to paypal payment handler
     *
     * @return array
     */
    protected function processPaypal()
    {
        $paypal = (new Paypal());

        $paymentResponse = $paypal->complete([
            'amount' => $paypal->formatAmount($this->amount),
            'token' => Session::get("checkout.{$this->uid}.stripe.token"),
            'payerid' => Session::get("checkout.{$this->uid}.stripe.payerid"),
            'currency' => 'GBP',
        ])->send();

        return $paymentResponse->getData();
    }

    /**
     * Process a free payment
     *
     * @return array
     */
    protected function processFree()
    {
        return [
            'freeorder' => 'success',
            'reference' => $this->orderID,
        ];
    }
}
