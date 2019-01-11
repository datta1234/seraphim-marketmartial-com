<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

class ItemRule implements Rule
{

    private $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
       
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
        switch ($value['title']) {
            case 'Future':
            case 'Future 1':
            case 'Future 2':
                if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                {
                    $this->message = "please enter a valid Future value";
                    return false;
                }
                break;
            case 'Contract':
                if($value['value'] !== null)
                {

                    if($value['value'] == 0)
                    {
                        return true;
                    }

                    if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                    {
                        $this->message = "please enter a valid Contracts value";
                        return false;     
                    }  
                }
                break;
            case 'Spot':
                if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                {
                    $this->message = "please enter a valid Spot value";
                    return false;
                }
                break;
            default:
                $this->message = "Invalid field";
                return false;
                break;
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
        return $this->message;
    }
}
