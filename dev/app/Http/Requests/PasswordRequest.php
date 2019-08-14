<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ConfirmOldPassword;
use App\Rules\MicrosoftPasswordPolicy;

class PasswordRequest extends FormRequest
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
            'old_password' => ['required',new ConfirmOldPassword()],
            'password' => [
                'required',
                'confirmed',
                'string',
                new MicrosoftPasswordPolicy
            ],
        ];
    }
}
