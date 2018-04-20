@extends('layouts.default')

@section('pageTitle', __('maxfactor::checkout.cart_title'))

@section('siteModifier', 'no--sticking site--buffer')

@section('headerModifier', 'no--sticking')

@section('main')

    <section class="user-heading">
        <div class="user-heading__title">
            <h1>@lang('maxfactor::checkout.cart_title')</h1>
        </div>
    </section>

    <section class="cart">
        <div class="cart__group">

            <ul>
                <li>@lang('maxfactor::checkout.product')</li>
                <li></li>
                <li>@lang('maxfactor::checkout.unit_price')</li>
                <li>@lang('maxfactor::checkout.quantity')</li>
                <li>@lang('maxfactor::checkout.line_total')</li>
            </ul>

            <mx-cart-item
                v-for="cartItem in itemsCollection.all()" :key="cartItem.id" :item="cartItem"
                @increase="increaseQuantity" @decrease="decreaseQuantity"
                dusk="v-cartitem-component"
            ></mx-cart-item>

            <div class="cart__total-options">
                <div class="cart__total-notes">
                    <label>@lang('Notes for :company', ['company' => config('app.name')])</label>
                    <textarea v-model="activeCartCollection.notes"></textarea>
                </div>
                <div class="cart__continue">
                    <h3>@lang('Net total:') @{{ cartNetTotal | money}}</h3>
                    <h3>@lang('Tax total:') @{{ cartTaxTotal | money | default(' N/A') }}</h3>
                    <h3>@lang('Sub total:') @{{ cartSubTotal | money }}</h3>
                    <span>@lang('maxfactor::checkout.postage_at_checkout')</span>

                    <true-false :value="{{ config('maxfactor-checkout.minimum_order') }} && cartNetTotal >= {{ config('maxfactor-checkout.minimum_order') }}">
                        <div slot="false">
                            <span>@lang('maxfactor::checkout.minimum_order', ['value' => config('maxfactor-checkout.minimum_order')])</span>
                        </div>

                        <div slot-scope="cartNetTotal">
                            <button
                                data-url="{{ route('checkout.show', ['uid' => 'UUID']) }}"
                                class="btn-primary"
                                @click.prevent="prepareCheckout"
                            >@lang('maxfactor::checkout.checkout_button')</button>

                            <button
                                data-url="{{ route('checkout.store', ['uid' => 'UUID', 'paypalauth']) }}"
                                @click.prevent="prepareCheckout"
                                class="btn-paypal"
                            ></button>
                        </div>
                    </true-false>
                </div>
            </div>

        </div>
    </section>
@endsection
