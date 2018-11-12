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
            'accept' => 'sometimes|required|boolean'

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
        $validator->sometimes(['volatilities'], ['required_without:accept', new QuotesVolatilities($userMarketRequest)], function ($input) use ($userMarketRequest) {
            \Log::info([ "Market Struct Id: ", $userMarketRequest->trade_structure_id ]);
            return in_array($userMarketRequest->trade_structure_id, [2, 3, 4, 5, 8]);
        });
    }
}
