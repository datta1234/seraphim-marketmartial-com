<?php

namespace App\Http\Requests\Tradescreen\UserMarket;
use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;

class MarketNegotiationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
        $validator->sometimes(['is_repeat','offer'], 'required|boolean', function ($input) {
            return is_null($input->bid) && is_null($input->offer);
        }); 

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
    }
}
