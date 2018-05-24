<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
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
            'email.*.email' => 'required|email',
            'email.*.default_id' => 'required',
            'email.*.notifiable' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'email.*.email.required' => 'Email field is required',
            'email.*.email.email' => 'Email field should be a valid email address',
        ];  
    }
}
