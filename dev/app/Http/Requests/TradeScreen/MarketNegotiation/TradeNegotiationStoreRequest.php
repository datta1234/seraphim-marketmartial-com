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
            'quantity' => 'required|numeric',
            'is_distpute' => 'boolean',
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

    /**
    * 
    *
    * @param \Illuminate\Contracts\Validation\Validator $validator
    * @return void
    */
    public function withValidator(Validator $validator)
    {
        $negotiation = $this->market_negotiation;
        $isOffer = $this->get('is_offer');
        $validator->after(function ($validator) use ($negotiation, $isOffer) {
            if($negotiation->isTradeAtBestOpen()) {
                if($negotiation->cond_buy_best == true) {
                    // if BUY @ best && OFFER
                    if($isOffer !== true) {
                        $validator->errors()->add('offer', "Only the initiator can trade this level");
                    }
                } else {
                    // if SELL @ best && BID
                    if($isOffer !== false) {
                        $validator->errors()->add('bid', "Only the initiator can trade this level");
                    }
                }
            }
        });
    }

}
