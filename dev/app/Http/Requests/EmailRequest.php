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
        $rules = [
            'email.*.email' => 'nullable|email',
            'email.*.notifiable' => 'required|boolean',
        ];

        if(!$this->has('email')) {
            return [
                'email' => 'required'
            ];
        }

        // Phase 3 update - Only Direct is suppose to be required, fixed an issue with order changing with custom emails added
        if(is_array($this->input('email')) && count($this->input('email')) > 0 ) {
            // Direct email is always required, assume id here as it is seeded and should always be 1
            $direct_index = null;
            foreach ($this->input('email') as $index => $value) {
                if(array_key_exists('default_id' , $value) && $value['default_id'] == 1) {
                    $direct_index = $index;
                }
            }

            if(!is_null($direct_index)) {
                $rules['email.'.$direct_index.'.email'] = 'required';
                return $rules;     
            }
        }
        // Default
        $rules['email.0.email'] = 'required';

        return $rules;
    }

    public function messages()
    {
        return [
            'email' => 'Emails are required',
            'email.*.email.required' => 'Email field is required',
            'email.*.email.email' => 'Email field should be a valid email address',
        ];  
    }
}
