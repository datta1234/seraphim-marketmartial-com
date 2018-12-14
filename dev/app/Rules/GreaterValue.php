<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GreaterValue implements Rule
{
    private $evaluate_against;
    private $field_name;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($evaluate_against, $field_name)
    {
        $this->evaluate_against = $evaluate_against;
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
        return $value > $this->evaluate_against;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The '.$this->field_name.' must be greater than the previous '.$this->field_name.'.';
    }
}
