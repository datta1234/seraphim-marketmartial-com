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
        dd('LOL');
        /*if($this->has('is_on_hold') && $this->userMarket) {
            return true
        }*/
        //check if current user org is the org related to the User Market Request linked to this user market id
        //allow changes to is_on_hold only
        // this->has(is_on_hold) && this->userMarket->userMarketRequest->user->org_id == this->user->org_id
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
