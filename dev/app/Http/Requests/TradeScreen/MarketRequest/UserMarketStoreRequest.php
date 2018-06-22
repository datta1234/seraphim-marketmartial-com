<?php

namespace App\Http\Requests\TradeScreen\MarketRequest;

use Illuminate\Foundation\Http\FormRequest;

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
        $market_condition = (new \App\Models\Market\MarketCondition)->getTable();
        return [
            'user_market_request_id'    =>  'required|exists:'.$request_table.',id',
            'current_market_negotiation'    =>  'required',
            
            // initial negotiation
            'current_market_negotiation.bid'     =>  'required_without:current_market_negotiation.offer|nullable|numeric',
            'current_market_negotiation.offer'   =>  'required_without:current_market_negotiation.bid|nullable|numeric',
            'current_market_negotiation.bid_qty'     =>  'required|numeric',
            'current_market_negotiation.offer_qty'   =>  'required|numeric',
            'current_market_negotiation.has_premium_calc'     =>  'boolean',
            'current_market_negotiation.bid_premium'     =>  'required_if:current_market_negotiation.has_premium_calc,true|nullable|numeric',
            'current_market_negotiation.offer_premium'   =>  'required_if:current_market_negotiation.has_premium_calc,true|nullable|numeric',

            // conditions
            'current_market_negotiation.conditions' => 'array',
            'current_market_negotiation.conditions.*.id' => 'required_with:current_market_negotiation.conditions|exists:'.$market_condition.',id',
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
            'current_market_negotiation.bid'    =>  "A negotiation bid/offer value required",
            'current_market_negotiation.offer'  =>  "A negotiation bid/offer value required",
            'current_market_negotiation.bid_qty'    =>  "A negotiation bid/offer quantity required",
            'current_market_negotiation.offer_qty'  =>  "A negotiation bid/offer quantity required",
            'current_market_negotiation.has_premium_calc'   =>  "",
            'current_market_negotiation.bid_premium'    =>  "",
            'current_market_negotiation.offer_premium'  =>  "",
            'current_market_negotiation.conditions' =>  "",
            'current_market_negotiation.conditions.*.id'    =>  "",
        ];
    }
}
