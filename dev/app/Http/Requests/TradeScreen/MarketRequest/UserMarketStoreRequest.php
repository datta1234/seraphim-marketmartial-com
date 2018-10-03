<?php

namespace App\Http\Requests\TradeScreen\MarketRequest;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class UserMarketStoreRequest extends FormRequest
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
        $request_table = (new \App\Models\MarketRequest\UserMarketRequest)->getTable();
        return [
            'user_market_request_id'    =>  'required|exists:'.$request_table.',id',
            'current_market_negotiation'    =>  'required',
            
            // initial negotiation
            'current_market_negotiation.has_premium_calc'     =>  'boolean',
            'current_market_negotiation.bid_premium'     =>  'required_if:current_market_negotiation.has_premium_calc,true|nullable|numeric',
            'current_market_negotiation.offer_premium'   =>  'required_if:current_market_negotiation.has_premium_calc,true|nullable|numeric',
        ];
    }

    /**
    *
    * @return array
    */
    public function messages() {
        return [
            'user_market_request_id'    =>  "The requested market was invalid",
            'current_market_negotiation'    =>  "Negotiation values are required",
            'current_market_negotiation.bid.required'    =>  "A negotiation bid value is required",
            'current_market_negotiation.offer.required'  =>  "A negotiation offer value is required",
            'current_market_negotiation.bid_qty.required'    =>  "A negotiation bid quantity is required",
            'current_market_negotiation.offer_qty.required'  =>  "A negotiation offer quantity is required",

            'current_market_negotiation.bid.numeric'    =>  "The bid must be a number.",
            'current_market_negotiation.offer.numeric'  =>  "The offer must be a number",
            'current_market_negotiation.bid_qty.numeric'    =>  "The bid quantity must be a number",
            'current_market_negotiation.offer_qty.numeric'  =>  "The offer quantity must be a number",

            'current_market_negotiation.has_premium_calc'   =>  "",
            'current_market_negotiation.bid_premium'    =>  "",
            'current_market_negotiation.offer_premium'  =>  "",
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
     

        $validator->sometimes(['current_market_negotiation.bid'], 'required_without_all:is_repeat,offer|nullable|numeric', function ($input) {
            return !is_null($input->current_market_negotiation["bid_qty"]);
        }); 

        $validator->sometimes(['current_market_negotiation.offer'], 'required_with:offer_qty|required_without_all:is_repeat,bid|nullable|numeric', function ($input) {
            return !is_null($input->current_market_negotiation["offer_qty"]);
        }); 

        $validator->sometimes(['current_market_negotiation.bid_qty'], 'required|numeric', function ($input) {
            return !is_null($input->current_market_negotiation["bid"]);
        }); 

        $validator->sometimes(['current_market_negotiation.offer_qty'], 'required_with:offer|numeric', function ($input) {
            return !is_null($input->current_market_negotiation["offer"]);
        }); 
    }
}
