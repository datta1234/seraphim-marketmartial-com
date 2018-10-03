<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TradeStructureGroupChoice implements Rule
{
    private $containsOnlyNull;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($tradeStructure)
    {
        $forceSelects = $tradeStructure->tradeStructureGroups->pluck('force_select');
        $this->containsOnlyNull = count(array_unique($forceSelects->toArray())) === 1 && $forceSelects->last() === null;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $selectInput = collect($value)->pluck('is_selected');
        $containsOnlyFalseInput = count(array_unique($selectInput->toArray())) === 1 && $selectInput->last() == false;
        return $this->containsOnlyNull == true ? !$containsOnlyFalseInput : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please ensure that you select a choice.';
    }
}
