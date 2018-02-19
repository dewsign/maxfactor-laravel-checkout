<?php

namespace Maxfactor\Checkout\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Maxfactor\Checkout\Handlers\Paypal;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Maxfactor\Checkout\Handlers\Payment;
use Illuminate\Support\Facades\Validator;
use Maxfactor\Checkout\Contracts\Postage;
use Maxfactor\Checkout\Contracts\Checkout;

trait HandlesCheckout
{
    /**
     * The keymap is used to map checkout keys to google tag manger keys
     *
     * @var array
     */
    protected $keyMap = [
        'id' => 'ref',
        'unitPrice' => 'price',
    ];

    protected $uid;

    /**
     * Get the current checkout id from the route object
     *
     * @return string
     */
    public function getCurrentCheckoutId()
    {
        return request()->route('uid') ? : '';
    }

    /**
     * Retrieve the checkout parameters from the Request or Session if available
     *
     * @return array
     */
    public function getCurrentCheckoutParams()
    {
        $uid = $this->getCurrentCheckoutId();

        return Request::has('uid')
            ? Request::all()
            : optional(Session::get("checkout.{$uid}"))->toArray() ? : [];
    }

    /**
     * Process different stages of checkout. Sets the template and runs the
     * method for the checkout stage if it exists.
     *
     * @param string $stage
     * @return Model
     */
    public function stage(string $stage = null, string $mode = 'show')
    {
        $uid = $this->uid = Route::current()->parameter('uid');

        if (!$stage) {
            return $this;
        }

        $this->template($stage);
        $this->append('stage', $stage);

        Session::put('js_vars', collect([
            'uid' => $uid,
            "checkout.{$uid}" => $this->get('items'),
            "checkout.shipping.{$uid}" => $this->get('shipping'),
            "checkout.billing.{$uid}" => $this->get('billing'),
            "checkout.user.{$uid}" => $this->get('user'),
            "stage.{$uid}" => $this->getFirst('stage'),
        ])->filter(function ($value, $key) {
            if ($value instanceof Collection) {
                return $value->count();
            }

            return $value !== "";
        })->all());

        Session::put('checkoutUID', $this->getCurrentCheckoutId());

        if ($stage == 'default' && $mode == 'show' && Request::has('token') && Request::has('PayerID')) {
            $this->propagatePaypal();
        }

        $processCheckoutStage = sprintf("checkoutStage%s%s", ucfirst($mode), ucfirst($stage));
        if (method_exists($this, $processCheckoutStage)) {
            $this->{$processCheckoutStage}();
        }

        return $this;
    }

    private function syncSession()
    {
        if (Request::get("checkout")) {
            Session::put("checkout.{$this->uid}", collect(Request::all()));

            Session::put('js_vars', collect([
                'uid' => $this->uid,
                "checkout.{$this->uid}" => Request::get('checkout')['items'],
                "checkout.shipping.{$this->uid}" => Request::get('checkout')['shipping'],
                "checkout.billing.{$this->uid}" => Request::get('checkout')['billing'],
                "checkout.user.{$this->uid}" => Request::get('checkout')['user'],
                "stage.{$this->uid}" => $this->getFirst('stage'),
            ])->filter(function ($value, $key) {
                if ($value instanceof Collection) {
                    return $value->count();
                }

                return $value !== "";
            })->all());
        }
    }

    /**
     * Additional functionality required for the Shipping stage
     *
     * @return void
     */
    public function checkoutStageShowShipping()
    {
        $postage = App::make(Postage::class, [
            'content' => null,
            'params' => Session::get("checkout.{$this->uid}", collect(['checkout' => []]))->toArray()
        ]);

        // add postage rates to model response
        $this->append('postageOptions', $postage->raw());
    }

    /**
     * Save the progress of the checkout with the submitted customer information
     * for the first stage of checkout.
     *
     * @return void
     */
    public function checkoutStageStoreShipping()
    {
        $this->syncSession();

        Validator::make(Request::get('checkout')['user'], [
            'email' => 'required|email',
            'telephone' => 'required',
        ])->validate();

        Validator::make(Request::get('checkout')['shipping'], [
            'firstname' => 'required|string',
            'surname' => 'required|string',
            'company' => 'nullable|string',
            'address' => 'required|string',
            'address_2' => 'nullable|string',
            'address_3' => 'nullable|string',
            'address_city' => 'required|string',
            'address_county' => 'required|string',
            'address_postcode' => 'required|string',
            'address_country' => 'required|string',
        ])->validate();
    }

    public function checkoutStageShowPayment()
    {
        return $this->append('stripePublishableKey', env('STRIPE_PUBLISHABLE_KEY') ? : '');
    }

    /**
     * The user has selected their shipping method. We need to store this
     * progress in the session and validate the method.
     *
     * @return void
     */
    public function checkoutStageStorePayment()
    {
        $this->syncSession();

        Validator::make(Request::get('checkout')['shippingMethod'], [
            'id' => 'required|integer|min:1',
        ])->validate();
    }

    /**
     * This is called after the client side is ready to process the actual
     * payment. We need to validate any fields, send the payment to the gateway
     * and return the result. The front-end will handle any redirects/ui.
     *
     * @return void
     */
    public function checkoutStageStoreComplete()
    {
        $this->syncSession();

        $provider = isset(Request::get('checkout')['payment']['provider']) ?
            Request::get('checkout')['payment']['provider'] : 'stripe';

        if ($provider == 'stripe') {
            Validator::make(Request::get('checkout')['billing'], [
                'nameoncard' => 'required|string',
            ])->validate();

            Validator::make(Request::get('checkout')['payment']['token'], [
                'id' => 'required|string',
                'object' => 'required|string',
                'type' => 'required|string',
            ])->validate();
        }

        Validator::make(Request::get('checkout')['user'], [
            'terms' => 'required|accepted',
        ])->validate();

        if (Request::get('checkout')['useShipping'] === false) {
            Validator::make(Request::get('checkout')['billing'], [
                'firstname' => 'required|string',
                'surname' => 'required|string',
                'company' => 'nullable|string',
                'address' => 'required|string',
                'address_2' => 'nullable|string',
                'address_3' => 'nullable|string',
                'address_city' => 'required|string',
                'address_county' => 'required|string',
                'address_postcode' => 'required|string',
                'address_country' => 'required|string',
            ])->validate();
        }

        if ($provider == 'paypal') {
            $paypal = (new Paypal());

            $paymentResponse = $paypal->complete([
                'amount' => $paypal->formatAmount($this->getFirst('finalTotal')),
                'token' => Session::get("checkout.{$this->uid}.stripe.token"),
                'payerid' => Session::get("checkout.{$this->uid}.stripe.payerid"),
                'currency' => 'GBP',
            ])->send();
        } else {
            $token = Request::get('checkout')['payment']['token']['id'];
            $amount = floatval($this->getFirst('finalTotal'));
            $orderReference = $this->getFirst('orderID');

            $paymentResponse = (new Payment())
                ->token($token)
                ->amount($amount)
                ->reference($orderReference)
                ->charge();

            Session::put('paymentResponse', collect($paymentResponse->getData()));
            $this->append('paymentResponse', collect($paymentResponse->getData()));
        }
        /**
         * Send the payment response to the Api for processing
         */
        $newCheckout = App::make(Checkout::class, [
            'uid' => $this->getFirst('uid'),
            'params' => [
                'checkout' => collect(Request::get('checkout'))->toArray(),
                'paymentResponse' => $paymentResponse->getData(),
            ]
        ]);

        return $this;
    }

    /**
     * The final stage of the checkout. This is where we show the result
     * or confirmation to the user
     *
     * @return void
     */
    public function checkoutStageShowComplete()
    {
        $this->renderGoogleTagManager();

        return $this;
    }

    /**
     * The user has selected to pay with PayPal. Store in session and
     * continue to show method.
     *
     * @return void
     */
    public function checkoutStageStorePaypalauth()
    {
        $this->syncSession();

        return $this;
    }

    /**
     * Perform PayPal payment authorization.  This is being done in a show
     * method to avoid cross origin resource problems.
     *
     * @return void
     */
    public function checkoutStageShowPaypalauth()
    {
        $this->syncSession();

        $paypal = (new Paypal());

        $response = $paypal->authorize([
            'amount' => $paypal->formatAmount($this->getFirst('finalTotal')),
            'transactionId' => $this->getFirst('orderID'),
            'currency' => 'GBP',
            'cancelUrl' => $paypal->getCancelUrl(),
            'returnUrl' => $paypal->getReturnUrl($this->uid),
        ])->send();

        if ($response->isRedirect()) {
            $response->redirect();
        }
    }

    /**
     * After PayPal authorization is complete, store response data in
     * Session to be accessable through JS variables.
     *
     * @return void
     */
    private function propagatePaypal()
    {
        $paypal = new PayPal;

        $request = $paypal->fetchExpressCheckout(Request::only('token'));

        $response = $request->send();

        if ($response->isSuccessful()) {
            $paypalData = $response->getData();

            Session::put('js_vars', [
                "uid" => $this->uid,
                "checkout.shipping.{$this->uid}" => collect([
                    'firstname' => $paypalData['FIRSTNAME'],
                    'surname' => $paypalData['LASTNAME'],
                    'address' => $paypalData['SHIPTOSTREET'],
                    'address_city' => $paypalData['SHIPTOCITY'],
                    'address_county' => $paypalData['SHIPTOSTATE'],
                    'address_postcode' => $paypalData['SHIPTOZIP'],
                    'address_country' => $paypalData['SHIPTOCOUNTRYCODE'],
                ]),
                "checkout.billing.{$this->uid}" => collect([
                    'firstname' => $paypalData['FIRSTNAME'],
                    'surname' => $paypalData['LASTNAME'],
                    'address' => $paypalData['SHIPTOSTREET'],
                    'address_city' => $paypalData['SHIPTOCITY'],
                    'address_county' => $paypalData['SHIPTOSTATE'],
                    'address_postcode' => $paypalData['SHIPTOZIP'],
                    'address_country' => $paypalData['SHIPTOCOUNTRYCODE'],
                ]),
                "checkout.user.{$this->uid}" => collect([
                    'email' => $paypalData['EMAIL'],
                ]),
                "stage.{$this->uid}" => 'default',
                "paypal.{$this->uid}" => collect([
                    'provider' => 'paypal',
                    'token' => $paypalData['TOKEN'],
                    'payerid' => $paypalData['PAYERID'],
                    'result' => '',
                ]),
            ]);
        }
    }

    /**
     * Pushes details of the transaction into the GTM data layer which is then
     * rendered on the page.
     *
     * @return void
     */
    public function renderGoogleTagManager()
    {
        $productsOrdered = collect(Session::get("checkout.{$this->uid}.checkout")['items'])->map(function ($item) {
            return collect($item)->mapWithKeys(function ($value, $key) {
                return [array_key_exists($key, $this->keyMap) ? $this->keyMap[$key] : $key => $value];
            })->only([
                'ref',
                'name',
                'category',
                'price',
                'quantity',
            ]);
        });

        return [
            'transactionId' => $this->getFirst('orderID'),
            'transactionAffiliation' => config('app.name'),
            'transactionTotal' => floatval($this->getFirst('finalTotal')),
            'transactionTax' => floatval($this->getFirst('incTaxTotal') - $this->getFirst('exTaxTotal')),
            'transactionShipping' => floatval($this->getFirst('postageTotal')),
            'transactionProducts' => $productsOrdered,
        ];
    }
}
