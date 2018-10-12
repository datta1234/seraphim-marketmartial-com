<?php
namespace App\Http\Requests\TradeScreen\UserMarket;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use App\Rules\LevelsImprovement;

class MarketNegotiationRequest extends FormRequest
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
        $validator->sometimes('bid', ['required_with:bid_qty','required_without_all:is_repeat,offer','nullable','numeric',new LevelsImprovement($this)], function ($input) {
            return !is_null($input->bid_qty) && !$input->is_repeat;
        }); 

        $validator->sometimes('offer', ['required_with:offer_qty','required_without_all:is_repeat,bid','nullable','numeric',new LevelsImprovement($this)], function ($input) {
            return !is_null($input->offer_qty) && !$input->is_repeat;
        }); 

        $validator->sometimes('bid_qty', 'required|numeric', function ($input) {
            return !is_null($input->bid) && !$input->is_repeat;
        }); 

        $validator->sometimes('offer_qty', 'required_with:offer|numeric', function ($input) {
            return !is_null($input->offer) && !$input->is_repeat;
        }); 
    }
}
