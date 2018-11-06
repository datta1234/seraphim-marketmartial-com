<?php

namespace App\Http\Requests\TradeScreen\MarketNegotiation;

use \Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use \App\Rules\PreventSelfTrade;

class TradeNegotiationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // @TODO: authorize for traders only
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_offer' => ['required','boolean',new PreventSelfTrade($this->market_negotiation,$this->user())],
            'quantity' => 'required|numeric'
        ];
    }

    /**
    *
    * @return array
    */
    public function messages() {
        return [
            'is_offer.boolean'    =>  "Ensure that the is offer field is a boolean",
        ];
    }

}
