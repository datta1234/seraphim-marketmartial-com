<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TradeStructureFeesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'trade_structures' => 'required|array',
            'trade_structures.*.id' => 'required|numeric',
            'trade_structures.*.fee_percentage' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'trade_structures.*.fee_percentage.required' => 'All fee percentages are required, please fill in for each structure.',
            'trade_structures.*.fee_percentage.numeric' => 'All fee percentages must be a numeric value.',
        ];  
    }
}
