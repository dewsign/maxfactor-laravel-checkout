<?php

namespace Maxfactor\Checkout\Handlers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Maxfactor\Checkout\Handlers\Paypal;
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

    private function cacheKey()
    {
        return sprintf('payment.response.%s.%s', str_slug($this->orderID), str_slug($this->amount));
    }

    /**
     * Allow a maximum of 1 payment to be processed every second for the same order and the same amount.
     * This should typically never happen unless the front-end sent multiple requests simultaneously.
     *
     * @return void
     */
    private function isAlreadyProcessingPayment()
    {
        if (Cache::has($this->cacheKey())) {
            clock()->warning('already processing payment');
            return true;
        };

        Cache::put($this->cacheKey(), 'processing', 10/60);

        return false;
    }

    /**
     * Delegate to relevant processing method
     *
     * @return array
     */
    public function process()
    {
        $paymentMethod = sprintf("process%s", ucfirst($this->provider));

        if ($this->isAlreadyProcessingPayment()) {
            return [];
        }

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

        Cache::put($this->cacheKey(), 'processed', 2/60);

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

        Cache::put($this->cacheKey(), 'processed', 2/60);

        return $paymentResponse->getData();
    }

    /**
     * Process a free payment
     *
     * @return array
     */
    protected function processFree()
    {
        Cache::put($this->cacheKey(), 'processed', 2/60);

        return [
            'freeorder' => 'success',
            'reference' => $this->orderID,
        ];
    }
}
