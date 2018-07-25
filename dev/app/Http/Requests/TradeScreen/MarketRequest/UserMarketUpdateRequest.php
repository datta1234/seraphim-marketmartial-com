<?php

namespace App\Http\Requests\TradeScreen\MarketRequest;

use Illuminate\Foundation\Http\FormRequest;

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
        if($this->has('is_on_hold') && $this->user_market->userMarketRequest->user->organisation_id == \Auth::user()->organisation_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   //add rules for is_on_hold and correlating message
        return [
            'is_on_hold' => 'required|boolean'
        ];
    }
}
