<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LevelsImprovement implements Rule
{

   
    private $request; 

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
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
        $this->request->user_market->load('userMarketRequest');
        $lastNegotiation = $this->request->user_market->lastNegotiation;
        //check to see if the bid is improved or the offer
        if($this->request->user_market->userMarketRequest->getStatus($this->request->user()->organisation_id) == "negotiation-open")
        {
            if($this->request->input('bid') != $lastNegotiation->bid  && $this->request->input('offer') != $lastNegotiation->offer)
            {
                return false;
            }else
            {
                return true;
            }

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
        return 'Either improve the bid or the offer not both';
    }
}
