<?php

namespace App\Http\Requests\TradeScreen\Market;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\StructureItems\TradeStructure;
use App\Rules\TradeStructureGroupChoice;

class UserMarketRequestRequest extends FormRequest
{
    private $tradeStructure;
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
        //fly selected, outright -> force yes
        //efp switch, risky, caly,options swicth, ->no force select
    
    
       $this->tradeStructure = TradeStructure::where('title',$this->input('trade_structure'))->with(['tradeStructureGroups'=>function($q){
            $q->where("trade_structure_group_type_id",1)
                ->with('items.itemType');
           }])->first();
       
       $rules = [
            'trade_structure' => 'required|exists:trade_structures,title', 
        ];
       
        //if tradestructure doesnt exist you will only get one valudation error on trade structure, else will be applied per structure
        if($this->tradeStructure)
        {
            $rules = array_merge($rules, $this->generate($this->tradeStructure));   
        }
        return $rules;
    }

    private function generate($tradeStructure)
    {  
        $rules = [];

        
        $rules['trade_structure_groups'] =  [new TradeStructureGroupChoice($tradeStructure)];

        for($i = 0; $i < $tradeStructure->tradeStructureGroups->count(); $i++) 
        { 
            $tradeStructuregroup = $tradeStructure->tradeStructureGroups[$i];//earier to work with
            
            $stockInput = "trade_structure_groups.{$i}.stock";
            $marketInput = "trade_structure_groups.{$i}.market_id";


            $rules[$marketInput] = "required_without:{$stockInput}|exists:markets,id";
            $rules[$stockInput] = "required_without:{$marketInput}";

            if(is_null($tradeStructuregroup->force_select))
            {
                $rules["trade_structure_groups.{$i}.is_selected"] = 'boolean|required';
            }else
            {
               $rules["trade_structure_groups.{$i}.is_selected"] = 'boolean'; 
            }

            foreach ($tradeStructuregroup->items as $structureItem)
            {
                $rules["trade_structure_groups.{$i}.fields.{$structureItem->title}"] = $structureItem->itemType->validation_rule;
            }
        }
        return $rules;
    }

    public function messages()
    {
        $rules = [];

        if($this->tradeStructure)
        {
            for($i = 0; $i < $this->tradeStructure->tradeStructureGroups->count(); $i++) 
            { 
                $tradeStructuregroup = $this->tradeStructure->tradeStructureGroups[$i];//earier to work with
                
          

                $rules["trade_structure_groups.{$i}.market_id.required_without"] = "Please set a market.";
                $rules["trade_structure_groups.{$i}.market_id.exists"] = "Please select a valid market.";
                $rules["trade_structure_groups.{$i}.stock.required_without"] = "Please select a valid stock.";
                $rules["trade_structure_groups.{$i}.is_selected.boolean"] = "Please select a valid choice";

                foreach ($tradeStructuregroup->items as $structureItem)
                {
                    $rules["trade_structure_groups.{$i}.fields.{$structureItem->title}.numeric"] = "Please ensure that you enter a valid ".$structureItem->title." amount.";
                    $rules["trade_structure_groups.{$i}.fields.{$structureItem->title}.date"] = "Please ensure that the selected ".$structureItem->title." is a valid date.";
                    $rules["trade_structure_groups.{$i}.fields.{$structureItem->title}.after"] = "Please select a date that is after today.";
                    $rules["trade_structure_groups.{$i}.fields.{$structureItem->title}.required"] = $structureItem->title." is required.";

                }
            }
        }
    
        return $rules;
    }
}
