<?php

namespace App\Http\Requests\TradeScreen\UserMarket;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use App\Rules\LevelsImprovement;
use App\Rules\MaintainsRatio;

class MarketNegotiationCounterRequest extends FormRequest
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
            // initial negotiation
            'has_premium_calc'          =>  'boolean',
            'is_repeat'                 =>  'boolean',
            'bid_premium'               =>  'required_if:has_premium_calc,true|nullable|numeric',
            'offer_premium'             =>  'required_if:has_premium_calc,true|nullable|numeric',
        ];
    }

    /**
    *
    * @return array
    */
    public function messages() {
        return [
            'user_market_request_id'    =>  "The requested market was invalid",
            'bid'                       =>  "A negotiation bid/offer value required",
            'offer'                     =>  "A negotiation bid/offer value required",
            'bid_qty'                   =>  "A negotiation bid/offer quantity required",
            'offer_qty'                 =>  "A negotiation bid/offer quantity required",
            'has_premium_calc'          =>  "",
            'bid_premium'               =>  "",
            'offer_premium'             =>  "",
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
        // get latest non mim
        $lastNegotiation = $this->market_negotiation;
        $ratio = $this->user_market->firstNegotiation->ratio;

        $validator->sometimes('bid', ['required_with:bid_qty','required_without_all:is_repeat,offer','nullable','numeric'], function ($input) {
            return !is_null($input->bid_qty) && !$input->is_repeat;
        });
        $validator->sometimes('bid', [new LevelsImprovement($this, $lastNegotiation)], function($input) use ($lastNegotiation) {
            return $lastNegotiation->cond_buy_mid === null;
        });

        $validator->sometimes('offer', ['required_with:offer_qty','required_without_all:is_repeat,bid','nullable','numeric'], function ($input) {
            return !is_null($input->offer_qty) && !$input->is_repeat;
        }); 
        $validator->sometimes('offer', [new LevelsImprovement($this, $lastNegotiation)], function($input) use ($lastNegotiation) {
            return $lastNegotiation->cond_buy_mid === null;
        });

        $validator->sometimes('bid_qty', ['required','numeric', new MaintainsRatio($this, $ratio, $lastNegotiation)], function ($input) {
            return !is_null($input->bid) && !$input->is_repeat;
        }); 

        $validator->sometimes('offer_qty', ['required_with:offer','numeric'new MaintainsRatio($this, $ratio, $lastNegotiation)], function ($input) {
            return !is_null($input->offer) && !$input->is_repeat;
        }); 
    }
}
