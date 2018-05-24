<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
                'full_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'cell_phone' => 'required|numeric',
                'work_phone' => 'required|numeric',
                'organisation_id' => 'required_without:new_organistation',
                'new_organistation' => 'required_without:organisation_id|string|max:255'
        ];
    }
}
