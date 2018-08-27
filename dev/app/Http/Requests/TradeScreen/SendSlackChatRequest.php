<?php

namespace App\Http\Requests\TradeScreen;

use Illuminate\Foundation\Http\FormRequest;

class SendSlackChatRequest extends FormRequest
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
            'new_message' => 'required_without:quick_message|string|max:1000',
            'quick_message' => 'required_without:new_message|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'new_message.required' => 'Message is required',
            'new_message.string' => 'Only text messages are accepted',
            'new_message.max' => 'Messages cannot be larger than a 1000 characters',
            'quick_message.required' => 'Message is required',
            'quick_message.string' => 'Only text messages are accepted',
            'quick_message.max' => 'Messages cannot be larger than a 1000 characters',
        ];  
    }
}
