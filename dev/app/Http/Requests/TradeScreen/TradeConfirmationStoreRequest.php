<?php

namespace App\Http\Requests\TradeScreen;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use App\Rules\ItemRule;

class TradeConfirmationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // @TODO: authorize for traders only
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = $this->generate();   
        return $rules;
    }

    private function generate()
    {  
        $this->trade_confirmation->load(['futureGroups'=>function($q){
            $q->with('tradeConfirmationItems.item');
        }]);

        $rules = [];
        for($i = 0; $i < $this->trade_confirmation->futureGroups->count(); $i++) 
        { 
            $item = "structure_groups.{$i}.items.*";
            $futureLabel= "structure_groups.{$i}.items.*.title";
            $futureValue = "structure_groups.{$i}.items.*.value";
            
            $rules[$item] = [new ItemRule()];
            $rules[$futureLabel] = "required:required";
            $rules[$futureValue] = "required:required";
        }

        return $rules;
    }

}
