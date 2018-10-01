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

    <section class="checkout">
        <form>
        @include('maxfactor::checkout.components.header')
        <div class="checkout__group">
            <div class="checkout__left">
                <div class="checkout__left-content">
                    <div class="checkout__customer-info">
                        <h3>@lang('Delivery')</h3>
                        <div v-if="cartCollection.discount.delivery_message" class="checkout__delivery-message">
                            @{{ cartCollection.discount.delivery_message }}
                        </div>
                        @foreach ($postageOptions as $postageOption)
                            <div class="checkout__shipping-option">
                                <input type="radio" name="shipping" 
                                    :value='{!! json_encode($postageOption) !!}' 
                                    v-model="cartCollection.shippingMethod"
                                    id="shipping{{ ucfirst(str_slug($postageOption['name'], '')) }}"
                                >
                                <label for="shipping{{ ucfirst(str_slug($postageOption['name'], '')) }}">{{ $postageOption['name'] }}</label>
                                <span>&pound; {{ Format::money($postageOption['price'], $postageOption['poa'] ? 'TBC' : 'Free') }}</span>
                            </div>
                        @endforeach
                        <v-form-error field="id"></v-form-error>
                    </div>
                    @component('maxfactor::checkout.components.actions')
                        @slot('continueLabel', __('Continue to payment'))
                        @slot('continueUrl', route('checkout.show', ['uid' => $uid, 'stage' => 'payment']))
                        @slot('returnLabel', __('Return to customer information'))
                        @slot('returnUrl', route('checkout.show', ['uid' => $uid]))
                    @endcomponent
                    @include('maxfactor::checkout.components.legal')
                </div>
            </div>
            <div class="checkout__right">
                @include('maxfactor::checkout.components.items')
                @include('maxfactor::checkout.components.summary', ['editable' => false])
            </div>
        </div>
        </form>
    </section>

@endsection

@section('footer')
    {{--  Do not include the default footer  --}}
@endsection
