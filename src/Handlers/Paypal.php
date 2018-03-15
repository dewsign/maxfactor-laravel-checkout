<?php

namespace Maxfactor\Checkout\Handlers;

use Omnipay\Omnipay;

/**
 * Class PayPal
 * @package App
 */
class PayPal
{
    protected $currency = 'GBP';

    /**
     * Setup PayPal gateway
     *
     * @return ExpressGateway
     */
    public function gateway()
    {
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername(config('paypal.credentials.username'));
        $gateway->setPassword(config('paypal.credentials.password'));
        $gateway->setSignature(config('paypal.credentials.signature'));
        $gateway->setTestMode(config('paypal.credentials.sandbox'));

        return $gateway;
    }

    /**
     * Authorise a PayPal payment
     *
     * @param array $parameters
     * @return mixed
     */
    public function authorize(array $parameters)
    {
        $response = $this->gateway()
            ->authorize($parameters);

        return $response;
    }

    /**
     * Get PayPal express checkout data
     *
     * @param array $parameters
     * @return mixed
     */
    public function fetchExpressCheckout(array $parameters)
    {
        $response = $this->gateway()
            ->fetchCheckout($parameters);

        return $response;
    }

    /**
     * Perform a PayPal express gateway purchase
     *
     * @param array $parameters
     * @return mixed
     */
    public function purchase(array $parameters)
    {
        $response = $this->gateway()
            ->purchase($parameters);

        return $response;
    }

    /**
     * Complete a PayPal purchase after pre authorization
     *
     * @param array $parameters
     */
    public function complete(array $parameters)
    {
        $response = $this->gateway()
            ->completePurchase($parameters);

        return $response;
    }

    /**
     * Format payment amount ready to be passed to PayPal express
     *
     * @param $amount
     */
    public function formatAmount($amount)
    {
        return round(floatval($amount), 2);
    }

    /**
     * Get url for PayPal express cancel URL
     * Used when customer cancels PayPal payment off site
     *
     * @param $order
     */
    public function getCancelUrl()
    {
        return route('cart.index');
    }

    /**
     * Get url for PayPal express return URL
     * Used when returning from a successful PayPal payment / authorization
     *
     * @param $order
     */
    public function getReturnUrl($uid)
    {
        return route('checkout.store', ['uid' => $uid]);
    }
}
