<?php

namespace App\Http\Requests\Tradescreen\UserMarket;

use Illuminate\Foundation\Http\FormRequest;

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
            'is_repeat'     =>  'boolean',
            'bid'           =>  'required_without_all:is_repeat,offer|nullable|numeric',
            'offer'         =>  'required_without_all:is_repeat,bid|nullable|numeric',
            'bid_qty'       =>  'required|numeric',
            'offer_qty'     =>  'required|numeric'
        ];
    }
}
