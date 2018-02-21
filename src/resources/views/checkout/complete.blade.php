@extends('layouts.default')

@section('header')
    {{--  Do not include the default header  --}}
@endsection

@section('asideheader')
    {{--  Do not include the default aside header  --}}
@endsection

@section('menu')
    {{--  Do not include the default mega menu  --}}
@show

@section('siteModifier', 'site--checkout')

@section('main')

<section class="checkout checkout--complete">
    @include('maxfactor::checkout.components.header')
    <div class="checkout__group">
        <div class="checkout__left">
            <div class="checkout__left-content">
                @include('maxfactor::checkout.components.notice')
                <div class="checkout__confirm">
                    @if (isset($orderID))
                        <p>@lang('Order') {{ $orderID }}</p>
                    @endif
                    <h2>@lang('Thank you') @{{ currentCheckout.user.firstname }}</h2>
                    <h3>@lang('Your order is confirmed')</h3>
                    <p>@lang('Order updates will be sent to') <strong>@{{ currentCheckout.user.email }}</strong></p>
                </div>
                <div class="checkout__completed-shipping">
                    <div class="checkout__complete-pay-ship">
                        <p>@lang('Shipping method:') <strong>@{{ currentCheckout.shippingMethod.name }}</strong></p>
                    </div>
                    <div class="checkout__complete-address">
                        <strong>@lang('Shipping address')</strong>
                        <p>
                            @{{ currentCheckout.shipping.firstname }} @{{ currentCheckout.shipping.surname }}<br>
                            @{{ currentCheckout.shipping.company }}<br>
                            @{{ currentCheckout.shipping.address }}<br>
                            @{{ currentCheckout.shipping.address_2 }}<br>
                            @{{ currentCheckout.shipping.address_city }}<br>
                            @{{ currentCheckout.shipping.address_postcode }}<br>
                            @{{ currentCheckout.shipping.address_country }}
                        </p>
                    </div>
                    <div class="checkout__complete-address">
                        <strong>Billing address</strong>
                        <p>
                            @{{ currentCheckout.billing.firstname }} @{{ currentCheckout.billing.surname }}<br>
                            @{{ currentCheckout.billing.company }}<br>
                            @{{ currentCheckout.billing.address }}<br>
                            @{{ currentCheckout.billing.address_2 }}<br>
                            @{{ currentCheckout.billing.address_city }}<br>
                            @{{ currentCheckout.billing.address_postcode }}<br>
                            @{{ currentCheckout.billing.address_country }}
                        </p>
                    </div>
                </div>
                @include('maxfactor::checkout.components.legal')
            </div>
        </div>
        <div class="checkout__right">
            @include('maxfactor::checkout.components.items')
            @include('maxfactor::checkout.components.summary', ['editable' => false])
        </div>
    </div>
</section>

@endsection

@section('footer')
    {{--  Do not include the default footer  --}}
@endsection
