<?php

namespace App\Http\Requests\TradeScreen;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckSlackToken;

class ReceiveSlackChatRequest extends FormRequest
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
            'token' => ['required', new CheckSlackToken()],
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'Authorisation token is required',
        ];  
    }

}
