<?php

namespace Maxfactor\Checkout\Requests;

use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Http\FormRequest;

class PaypalPaymentRequest extends FormRequest
{
    protected $rules = [
        'checkout.user.terms' => 'required|accepted',
    ];

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
            $rules['checkout.billing.address_county'] = 'required|string';
            $rules['checkout.billing.address_postcode'] = 'required|string';
            $rules['checkout.billing.address_country'] = 'required|string';
        }

        return $rules;
    }
}