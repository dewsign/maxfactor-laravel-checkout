<?php

namespace Maxfactor\Checkout;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
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
    public function store(Request $request, $uid, $stage = 'default')
    {
        $checkout = App::make(Checkout::class, [
            'content' => $uid,
            'params' => [
                'checkout' => $request->get('checkout'),
            ]
        ]);

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
    public function show(Request $request, $uid, $stage = 'default')
    {
        $checkout = App::make(Checkout::class, [
            'content' => $uid,
            'params' => [
                Session::get("checkout.{$uid}") ?
                    Session::get("checkout.{$uid}")->toArray() : [],
            ]
        ]);

        return $checkout
            ->append('uid', $uid)
            ->stage($stage)
            ->render();
    }
}
