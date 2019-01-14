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
            'birthdate' => 'nullable|date',
            'is_married' => 'nullable|boolean',
            'has_children'=>'nullable|boolean',
            'hobbies'=>'nullable|string',
            'interest.*.value'=>'required|string'
        ];
    }

    public function messages()
    {
        return [
            'interest.*.value.required'=>'Which team or athlete do you support?'
        ];
    }
}
