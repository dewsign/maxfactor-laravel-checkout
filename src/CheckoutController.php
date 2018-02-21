<?php

namespace Maxfactor\Checkout;

use App\Http\Controllers\Controller;
use Maxfactor\Checkout\Contracts\Checkout;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO: get dynamic UUID from session / cookie
        session(['checkout_uid' => 'd5c7d19a6628a7787dbaac754213af95']);
        return redirect()->route('checkout.show', ['uid' => 'd5c7d19a6628a7787dbaac754213af95']);
    }

    /**
     * Validate any input and store the checkout progress. This will always be
     * received from an Ajax request so we need to return Json.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Checkout $checkout, $uid, $stage = 'default')
    {
        $result = $checkout
            ->stage($stage, 'store')
            ->append('uid', $uid)
            ->raw();

        return $result;
    }

    /**
     * The request to show a specific stage of checkout.
     *
     * @param  string $uid
     * @param  string $stage
     * @return \Illuminate\Http\Response
     */
    public function show(Checkout $checkout, $uid, $stage = 'default')
    {
        return $checkout
            ->append('uid', $uid)
            ->stage($stage)
            ->render();
    }
}
