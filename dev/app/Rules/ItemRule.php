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
                if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                {
                    $this->message = ["future" => "please enter a valid Future value"];
                    return false;
                }
                break;
            case 'Future 1':
                if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                {
                    $this->message = ["future_1" => "please enter a valid Future value"];
                    return false;
                }
                break;
            case 'Future 2':
                if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                {
                    $this->message = ["future_2" => "please enter a valid Future value"];
                    return false;
                }
                break;
            case 'Contract':
                /* Phase3 Update, no longer allowed to edit contracts*/
                /*if($value['value'] !== null)
                {
                    if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                    {
                        $this->message = ["contract" => "please enter a valid Contracts value"];
                        return false;     
                    }  

                    if($value['value'] == 0)
                    {
                        return true;
                    }
                }*/
                return true;
                break;
            case 'Spot':
                if(!filter_var($value['value'], FILTER_VALIDATE_FLOAT))
                {
                    $this->message = ["spot" => "please enter a valid Spot value"];
                    return false;
                }
                break;
            case 'is_put':
                if(!filter_var($value['value'], FILTER_VALIDATE_BOOLEAN))
                {
                    $this->message = ["is_put" => "please enter a valid Put or Call value"];
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
