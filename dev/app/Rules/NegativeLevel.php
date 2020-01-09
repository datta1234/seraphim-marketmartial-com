<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NegativeLevel implements Rule
{
    private $structure;
    private $field_name;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userMarketRequest, $field_name)
    {
        $this->structure = $userMarketRequest->trade_structure_slug;
        $this->field_name = $field_name;
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
        if(!in_array($this->structure, ['efp','rolls','efp_switch']) && $value < 0) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The '.$this->field_name.' cannot be negative';
    }
}
