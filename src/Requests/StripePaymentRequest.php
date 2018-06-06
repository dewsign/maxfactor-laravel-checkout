<?php

namespace Maxfactor\Checkout\Requests;

use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Http\FormRequest;

class StripePaymentRequest extends FormRequest
{
    protected $rules = [
        'checkout.billing.nameoncard' => 'required|string',
        'checkout.payment.token.id' => 'required|string',
        'checkout.payment.token.object' => 'required|string',
        'checkout.payment.token.type' => 'required|string',
        'checkout.user.terms' => 'required|accepted',
    ];

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'checkout.user.terms.required' => 'The terms must be accepted.',
            'checkout.user.terms.accepted'  => 'The terms must be accepted.',
            'checkout.billing.firstname.required'  => 'The firstname field is required.',
            'checkout.billing.surname.required'  => 'The surname field is required.',
            'checkout.billing.address.required'  => 'The address field is required.',
            'checkout.billing.address_city.required'  => 'The city field is required.',
            'checkout.billing.address_postcode.required'  => 'The postcode field is required.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = $this->rules;

        if (Request::get('checkout')['useShipping'] === false) {
            $rules['checkout.billing.firstname'] = 'required|string';
            $rules['checkout.billing.surname'] = 'required|string';
            $rules['checkout.billing.company'] = 'nullable|string';
            $rules['checkout.billing.address'] = 'required|string';
            $rules['checkout.billing.address_2'] = 'nullable|string';
            $rules['checkout.billing.address_3'] = 'nullable|string';
            $rules['checkout.billing.address_city'] = 'required|string';
            $rules['checkout.billing.address_county'] = 'nullable|string';
            $rules['checkout.billing.address_postcode'] = 'required|string';
            $rules['checkout.billing.address_country'] = 'nullable|string';
        }

        return $rules;
    }
}
