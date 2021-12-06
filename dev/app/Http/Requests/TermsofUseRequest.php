<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermsofUseRequest extends FormRequest
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
            'tc_accepted' => 'required'
        ];
    }

    public function messages()
    {
        return [
            /*'tc_accepted.accepted' => 'please ensure you accept the terms of use and the privacy policy'*/
        ];
    }
}
