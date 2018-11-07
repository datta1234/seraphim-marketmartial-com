<?php

namespace App\Http\Requests\TradeScreen\UserMarket;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use App\Rules\LevelsImprovement;
use App\Rules\MaintainsRatio;

class MarketNegotiationImproveBestRequest extends FormRequest
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
     * @return arraya
     */
    public function rules()
    {  
        return [
            // initial negotiation
            
        ];
    }

    /**
    *
    * @return array
    */
    public function messages() {
        return [
            
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
        $negotiation = $this->market_negotiation;
        $lastNegotiation = $this->user_market->lastNegotiation;

        if($negotiation->id == $lastNegotiation->id) {
            $validator->errors()->add('market_negotiation', 'Requested Negotiation is no longer the latest levels, Please try again.');
        } else {
            $ratio = $this->user_market->firstNegotiation->ratio;

            $validator->sometimes('bid', ['required_with:bid_qty','required_without_all:is_repeat,offer','nullable','numeric',new LevelsImprovement($this, $lastNegotiation)], function ($input) use($lastNegotiation) {
                return $lastNegotiation->cond_buy_best == false;
            }); 

            $validator->sometimes('offer', ['required_with:offer_qty','required_without_all:is_repeat,bid','nullable','numeric',new LevelsImprovement($this, $lastNegotiation)], function ($input) use($lastNegotiation) {
                return $lastNegotiation->cond_buy_best == true;
            }); 

            $validator->sometimes('bid_qty', ['required','numeric', new MaintainsRatio($this, $ratio, $lastNegotiation)], function ($input) use($lastNegotiation) {
                return $lastNegotiation->cond_buy_best == false;
            }); 

            $validator->sometimes('offer_qty', ['required_with:offer','numeric'new MaintainsRatio($this, $ratio, $lastNegotiation)], function ($input) use($lastNegotiation) {
                return $lastNegotiation->cond_buy_best == true;
            }); 
        }
    }
}
