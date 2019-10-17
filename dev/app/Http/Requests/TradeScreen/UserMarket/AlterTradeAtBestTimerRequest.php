<?php

namespace App\Http\Requests\TradeScreen\UserMarket;

use Illuminate\Foundation\Http\FormRequest;

class AlterTradeAtBestTimerRequest extends FormRequest
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
            'reset' => 'required_without:end|boolean',
            'end' => 'required_without:reset|boolean'
        ];
    }

    public function messages()
    {
        return [
            'reset.required' => 'Reset Timer is required',
            'reset.boolean' => 'Reset only accpect a valid option',
            'end.required' => 'End Timer is required',
            'end.boolean' => 'End only accpect a valid option',
        ];  
    }
}
