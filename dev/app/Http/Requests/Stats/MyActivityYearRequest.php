<?php

namespace App\Http\Requests\Stats;

use Illuminate\Foundation\Http\FormRequest;

class MyActivityYearRequest extends FormRequest
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
        /*
        *add validation rule for the following fields, although sidenote 
        *we typically never validate on search as the results also look at 
        *would be empty, if wrong data is passed
        *https://laravel.com/docs/5.7/validation#conditionally-adding-rules
        *filter_date
        *filter_market
        *filter_expiration
        */
        return [
            'year' => 'required|date_format:Y'
        ];
    }
}
