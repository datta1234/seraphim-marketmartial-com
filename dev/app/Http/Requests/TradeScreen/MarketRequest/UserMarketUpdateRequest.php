<?php

namespace App\Http\Requests\TradeScreen\MarketRequest;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use App\Rules\QuotesVolatilities;

class UserMarketUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {   
        // TODO move $this->user_market->userMarketRequest->user->organisation_id to model method on userMarketRequest
        // if($this->has('is_on_hold') && $this->user_market->userMarketRequest->user->organisation_id == \Auth::user()->organisation_id) {
        //     return true;
        // }
        //this code has been moved to a policy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   //add rules for is_on_hold and correlating message
        return [
            'is_on_hold' => 'sometimes|required|boolean',
            'accept' => 'sometimes|required|boolean',
            'is_repeat' => 'sometimes',
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
        $userMarketRequest = $this->user_market_request;
        
        $validator->sometimes(['bid'], 'required_without_all:is_repeat,offer|nullable|numeric', function ($input) {
            return !is_null($input->bid_qty);
        }); 

        $validator->sometimes(['offer'], 'required_with:offer_qty|required_without_all:is_repeat,bid|nullable|numeric', function ($input) {
            return !is_null($input->offer_qty);
        }); 

        $validator->sometimes(['bid_qty'], 'required|numeric', function ($input) {
            return !is_null($input->bid);
        }); 

        $validator->sometimes(['offer_qty'], 'required_with:offer|numeric', function ($input) {
            return !is_null($input->offer);

        }); 

        // Risky / Calendar / Fly
        $validator->sometimes(['volatilities'], ['required_without_all:accept,is_on_hold,is_repeat', new QuotesVolatilities($userMarketRequest)], function ($input) use ($userMarketRequest) {
            return in_array($userMarketRequest->trade_structure_id, [2, 3, 4, 5, 8]);
        });

        $is_on_hold = $this->input('is_on_hold') == true;
        $accept = $this->input('accept') == true;
        $is_repeat = $this->input('is_repeat') == true;
        $last_negotiation = $this->user_market->lastNegotiation;
        $bid = $this->input('bid');
        $offer = $this->input('offer');
        
        $validator->after(function ($validator) use ($last_negotiation, $is_on_hold, $accept, $is_repeat, $bid, $offer) {
            if(!$is_on_hold && !$accept && !$is_repeat) {
                if( $bid <= $last_negotiation->bid && $offer >= $last_negotiation->offer) {
                    $validator->errors()->add('levels', "Bid or Offer value needs to be improved");
                }
            }
        });
    }
}
