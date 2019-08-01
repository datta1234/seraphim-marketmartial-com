<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MicrosoftPasswordPolicy implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        //https://blogs.technet.microsoft.com/poshchap/2016/10/14/regex-for-password-complexity-validation/
        $match = [];
        preg_match('/^((?=.*[a-z])(?=.*[A-Z])(?=.*\d)|(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9])|(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9])|(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]))([A-Za-z\d@#$%^&£*\-_+=[\]{}|\\:\',?\/`~"();!]|\.(?!@)){8,16}$/', $value, $match);
        return count($match) > 0; 
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your password does not meet the minimum password requirements. Passwords should contain 8 or more characters, consisting of any 3 of the following,<br/>
        - Upper case letters<br/>
        - Lower case letters<br/>
        - Numeric characters<br/>
        - Non Alpha Numeric characters<br/>
        - Any Unicode character';
    }
}
