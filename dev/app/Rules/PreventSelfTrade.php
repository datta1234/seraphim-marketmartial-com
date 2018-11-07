<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PreventSelfTrade implements Rule
{
    private $marketNegotiation;
    private $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($marketNegotiation,$user)
    {
        $this->marketNegotiation = $marketNegotiation;
        $this->user = $user;
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
        $attr = $value ? 'offer' : 'bid';
        if(!$this->marketNegotiation->tradeNegotiations()->exists())
        {
            $source = $this->marketNegotiation->marketNegotiationSource($attr);        
            return $source->user->organisation_id != $this->user->organisation_id;
        }else
        {
          return true;//ensure that the policy does the rest  
        }
      
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are not allowed to trade on a level that you have set.';
    }
}
