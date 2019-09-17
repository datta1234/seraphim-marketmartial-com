<?php

namespace App\Http\Requests\TradeScreen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class SendSlackChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(isset($this->new_message) && !\Auth::user()->organisation->slack_text_chat) {
            return false;
        }

        return true;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException('This action is unauthorized. Please contact the Admin.');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $quick_message_ids = collect(config('marketmartial.slack.quick_messages'))->implode('id', ',');
        return [
            'new_message' => 'required_without:quick_message|string|max:1000',
            'quick_message' => 'required_without:new_message|integer|in:'.$quick_message_ids,
        ];
    }

    public function messages()
    {
        return [
            'new_message.required' => 'Message is required',
            'new_message.string' => 'Only text messages are accepted',
            'new_message.max' => 'Messages cannot be larger than a 1000 characters',
            'quick_message.required' => 'Message is required',
            'quick_message.integer' => 'Incorrect quick message option sent',
        ];  
    }
}
