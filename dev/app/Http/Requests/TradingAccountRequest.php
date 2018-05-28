<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TradingAccountRequest extends FormRequest
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
            'trading_accounts.*.safex_number' => 'required',
            'trading_accounts.*.market_id' => 'required',
            'trading_accounts.*.sub_account' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'trading_accounts.*.safex_number.required' => 'Safex number field is required',
            'trading_accounts.*.sub_account.required' => 'Sub account field is required',
        ];  
    }
}
