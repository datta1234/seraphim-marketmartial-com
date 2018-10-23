<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LevelsImprovement implements Rule
{

   
    private $request; 
    private $lastNegotiation;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request, $lastNegotiation = null)
    {
        $this->request = $request;
        $this->lastNegotiation = $lastNegotiation;
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
        if(!in_array($attribute, ['bid', 'offer'])) {
            return false;
        }
        $this->request->user_market->load('userMarketRequest');
        //check to see if the bid is improved or the offer
        if(in_array($this->request->user_market->userMarketRequest->getStatus($this->request->user()->organisation_id), ["negotiation-pending", "negotiation-open", "trade-negotiation-open"]))
        {
            // if the last one was an FOK & killed
            if($this->lastNegotiation->is_killed) {
                return true;
            }

            $inverse = ($attribute == 'bid' ? 'offer' : 'bid');
            $valid = ($attribute == 'bid');
            /*
                both 'bid' & 'offer' follow same process, only change is comparisson '<' vs '>' 

                if bid present
                    if bid improved
                        return true
                    if bid not improved
                        if offer improved
                            return true

                if offer present
                    if offer improved
                        return true
                    if offer not improved
                        if bid improved
                            return true
            */

            // ensure its set
            if($this->request->input($attribute) != null) {
                /*
                    Ensure the value is improved

                    if 'bid'
                        ( bid > curBid ) == true && 
                        ( bid < curBid ) == false
                    eg:
                        [10, 5] = true && true
                        [10, 15] = false && false

                    if 'offer'
                        ( offer > curOffer ) == false &&
                        ( offer < curOffer ) == true
                    eg:
                        [10, 15] = true && true
                        [10, 5] = false && false

                */
                if(
                    ($this->request->input($attribute) > $this->lastNegotiation->{$attribute}) == $valid &&
                    ($this->request->input($attribute) < $this->lastNegotiation->{$attribute}) == !$valid
                ) {
                    return true;
                } else {
                    /*
                        If Value not improved, Ensure the Inverse value is improved ONLY if attribute equal

                        if 'bid' > check offer
                            ( offer > curOffer ) == false && 
                            ( offer < curOffer ) == true
                        eg:
                            [10, 15] = true && true
                            [10, 5] = false && false

                        if 'offer' > check bid
                            ( bid > curBid ) == false &&
                            ( bid < curBid ) == true
                        eg:
                            [10, 5] = true && true
                            [10, 15] = false && false

                    */
                    if(
                        floatval($this->request->input($attribute)) === floatval($this->lastNegotiation->{$attribute}) &&
                        ($this->request->input($inverse) > $this->lastNegotiation->{$inverse}) == !$valid &&
                        ($this->request->input($inverse) < $this->lastNegotiation->{$inverse}) == $valid
                    ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Either improve the bid or the offer';
    }
}
