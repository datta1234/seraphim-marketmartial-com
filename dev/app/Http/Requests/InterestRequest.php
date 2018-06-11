<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterestRequest extends FormRequest
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
            'birthdate' => 'required|date',
            'is_married' => 'required|boolean',
            'has_children'=>'required|boolean',
            'hobbies'=>'required|string',
            'interest.*.value'=>'required|string'
        ];
    }

    public function messages()
    {
        return [
            'interest.*.value.required'=>'once you have checked and interest please complete the team.'
        ];
    }
}
