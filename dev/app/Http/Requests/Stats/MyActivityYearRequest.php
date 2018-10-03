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
        return [
            'year' => 'required|date_format:Y',
            'search' => 'string|nullable',
            '_order_by' => 'string|nullable',
            '_order' => 'string|nullable',
            'filter_date' => 'date_format:Y-m-d|nullable',
            'filter_market' => 'exists:markets,id|nullable',
            'filter_expiration' => 'date_format:d M Y|nullable',
            'is_my_activity' => 'boolean',
        ];
    }
}
