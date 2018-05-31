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
        <form name="checkout-form">
        @include('maxfactor::checkout.components.header')
        <div class="checkout__group">
            <div class="checkout__left">
                <div class="checkout__left-content">
                    <div class="checkout__customer-info">
                        <h1>@lang('Customer information')</h1>
                        <span class="checkout__existing-customer" v-if="!isLoggedIn && canEditShipping">@lang('Already have an account?')
                            Login or Logout
                        </span>
                        <span class="checkout__existing-customer" v-if="isLoggedIn && canEditShipping">@lang('You are logged in as') @{{ currentCheckout.shipping.firstname }}
                            <v-button @click.prevent="emit('logout')">@lang('Logout')</v-button>
                        </span>
                        <span v-if="!canEditShipping">@lang('The details below are not editable')</span>
                        <div class="user__field user__field--half">
                            <label for="addressEmail" class="label--required">@lang('Your email')</label>
                            <input id="addressEmail" type="email" v-model="currentCheckout.user.email" :disabled="isLoggedIn || !canEditShipping" required>
                            <v-form-error field="email"></v-form-error>
                        </div>
                        <div class="user__field user__field--half">
                            <label for="addressPhone" class="label--required">@lang('Phone')</label>
                            <input id="addressPhone" type="tel" v-model="currentCheckout.user.telephone" :disabled="!canEditShipping" required>
                            <v-form-error field="telephone"></v-form-error>
                        </div>
                        <div class="checkout__newsletter" v-if="canEditShipping">
                            <label><input type="checkbox" v-model="currentCheckout.user.newsletter">@lang('Sign up to our newsletter')</label>
                        </div>
                        <h3>@lang('Shipping address')</h3>

                        <div class="user__field user__field--half">
                            <label for="addressFirstName" class="label--required">@lang('First name')</label>
                            <input id="addressFirstName" type="text" v-model="currentCheckout.shipping.firstname" :disabled="!canEditShipping" required>
                            <v-form-error field="firstname"></v-form-error>
                        </div>
                        <div class="user__field user__field--half">
                            <label for="addressSurname" class="label--required">@lang('Surname')</label>
                            <input id="addressSurname" type="text" v-model="currentCheckout.shipping.surname" :disabled="!canEditShipping" required>
                            <v-form-error field="surname"></v-form-error>
                        </div>

                        <mx-address v-model="currentCheckout.shipping" api-key="{{ config('maxfactor-checkout.pca_key') }}" class="checkout__customer-info">
                            <template slot="selection" slot-scope="{ addresses, select, haveAddresses, clearAddresses }">
                                <postcode-modal
                                    :addresses="addresses"
                                    :value="haveAddresses"
                                    @input="clearAddresses"
                                    @select="select"
                                >
                                </postcode-modal>
                            </template>

                            <template slot-scope="{ address, changeAddress }">
                                <div class="user__field">
                                    <label for="addressCompany">@lang('Company')</label>
                                    <input id="addressCompany" type="text" v-model="address.company" :disabled="!canEditShipping">
                                    <v-form-error field="company"></v-form-error>
                                </div>
                                <div class="user__field user__field--half sort-order1">
                                    <label for="addressAddress" class="label--required">@lang('Address')</label>
                                    <input id="addressAddress" type="text" v-model="address.address" :disabled="!canEditShipping" required>
                                    <v-form-error field="address"></v-form-error>
                                </div>
                                <div class="user__field user__field--half sort-order2">
                                    <label for="addressAddress2">@lang('Address 2')</label>
                                    <input id="addressAddress2" type="text" v-model="address.address_2" :disabled="!canEditShipping">
                                    <v-form-error field="address_2"></v-form-error>
                                </div>
                                <div class="user__field user__field--half sort-order3">
                                    <label for="addressAddress3">@lang('Address 3')</label>
                                    <input id="addressAddress3" type="text" v-model="address.address_3" :disabled="!canEditShipping">
                                    <v-form-error field="address_3"></v-form-error>
                                </div>
                                <div class="user__field user__field--half sort-order4">
                                    <label for="addressCity" class="label--required">@lang('City')</label>
                                    <input id="addressCity" type="text" v-model="address.address_city" :disabled="!canEditShipping" required>
                                    <v-form-error field="address_city"></v-form-error>
                                </div>
                                <div class="user__field user__field--half sort-order1">
                                    <label for="addressCounty" class="label--required">@lang('County')</label>
                                    <input id="addressCounty" type="text" v-model="address.address_county" :disabled="!canEditShipping" required>
                                    <v-form-error field="address_county"></v-form-error>
                                </div>
                                <div class="user__field user__field--half sort-order2">
                                    <label for="addressPostcode" class="label--required">@lang('Post code')</label>
                                    <input id="addressPostcode" type="text" v-model="address.address_postcode" :disabled="!canEditShipping" required>
                                    <v-form-error field="address_postcode"></v-form-error>
                                    <button @click.prevent="changeAddress">Change address</button>
                                </div>
                                <div class="user__field user__field--half sort-order3 user__field--country">
                                    <label for="addressCountry" class="label--required">@lang('Country')</label>
                                    <div class="select">
                                        <select v-model="address.address_country" id="addressCountry" :disabled="!canEditShipping" required>
                                            <option selected disabled>Please select</option>
                                            @foreach (Maxfactor::countries() as $code => $item)
                                                <option value="{{ $code }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <v-form-error field="address_country"></v-form-error>
                                </div>
                            </template>
                        </mx-address>

                        <div class="user__field">
                            <label for="address_notes">@lang('Delivery Notes')</label>
                            <input id="address_notes" type="text" v-model="currentCheckout.shipping.address_notes" :disabled="!canEditShipping">
                        </div>
                        <div class="user__field user__field--half sort-order4" v-if="currentCheckout.taxOptional">
                            <label for="vatnumber" class="label--required">@lang('ECC VAT Number')</label>
                            <input id="vatnumber" type="text" v-model="currentCheckout.user.vat_number" :disabled="!canEditShipping" required>
                            <v-form-error field="vat_number"></v-form-error>
                        </div>
                    </div>
                    @component('maxfactor::checkout.components.actions')
                        @slot('continueLabel', __('Continue to shipping method'))
                        @slot('continueUrl', route('checkout.show', ['uid' => $uid, 'stage' => 'shipping']))
                        @slot('returnLabel', __('Return to cart'))
                        @slot('returnUrl', route('cart.index'))
                    @endcomponent
                    @include('maxfactor::checkout.components.legal')
                </div>
            </div>
            <div class="checkout__right">
                <div class="checkout__right-content">
                    @include('maxfactor::checkout.components.items')
                    <mx-cart-discount></mx-cart-discount>
                    @include('maxfactor::checkout.components.summary', ['editable' => false])
                </div>
            </div>
        </div>
        </form>
    </section>

@endsection

@section('footer')
    {{--  Do not include the default footer  --}}
@endsection
