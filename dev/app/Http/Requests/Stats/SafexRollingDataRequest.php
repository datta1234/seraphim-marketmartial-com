<?php

namespace App\Http\Requests\Stats;

use Illuminate\Foundation\Http\FormRequest;

class SafexRollingDataRequest extends FormRequest
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
            'search' => 'string|nullable',
            '_order_by' => 'string|nullable',
            '_order' => 'string|nullable',
            'filter_date' => 'date_format:Y-m-d|nullable',
            'filter_market' => 'string|nullable',
            'filter_expiration' => 'date_format:Y-m-d|nullable',
            'filter_nominal' => 'string|nullable',
        ];
    }
}
