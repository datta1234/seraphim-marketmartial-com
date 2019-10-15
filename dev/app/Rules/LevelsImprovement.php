<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LevelsImprovement implements Rule
{

   
    private $request; 
    private $lastNegotiation;

    private $message = 'Either improve the bid or the offer';

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
        $inverse = ($attribute == 'bid' ? 'offer' : 'bid');

        if(!in_array($attribute, ['bid', 'offer'])) {
            \Log::info($attribute." >>> 1");
            return false;
        }
        $this->request->user_market->load('userMarketRequest');

        $org_status = $this->request->user_market->userMarketRequest->getStatus($this->request->user()->organisation_id);

        \Log::info($org_status);
        //check to see if the bid is improved or the offer
        if(in_array($org_status, ["negotiation-pending", "negotiation-open", "trade-negotiation-open"]))
        {
           

            //if source is me i'm allowed to updated it need to re-check
            if($this->lastNegotiation->user->organisation_id == $this->request->user()->organisation_id)
            {
                $source = $this->lastNegotiation->marketNegotiationSource($attribute);
                
                if($source)
                {
                    if($this->request->user()->organisation_id == $source->user->organisation_id)
                    {
                        \Log::info($attribute." >>> 2");
                        return true;
                    }
                }
            }

            // if the last one was an FOK & killed
            if($this->lastNegotiation->is_killed) {
                $cond_att = $this->lastNegotiation->cond_fok_apply_bid ? 'bid' : 'offer';
                // if the attribute on the last killed FoK is the one under validation, ignore its value...
                if($attribute == $cond_att) {
                    if($attribute == 'bid') {
                        if($this->request->input('bid') >= $this->lastNegotiation->getLatest('offer')) {
                            $this->message = "Bid value cannot be equal or higher than Offer value.";
                            return false;
                        }
                    } else {
                        if($this->request->input('offer') <= $this->lastNegotiation->getLatest('bid')) {
                            $this->message = "Offer value cannot be equal or lower than Bid value.";
                            return false;
                        }
                    }
                }
            }

            // should handle MM-845
            if($this->request->input($attribute) != null && $this->request->input($inverse) != null) {
                $this->message = ($attribute == 'bid' ? 'Bid' : 'Offer')." value cannot be equal or ".($attribute == 'bid' ? 'higher' : 'lower' )." than $inverse value.";
                if($this->request->input('bid') >= $this->request->input('offer')) {
                    return false;
                }
            }

            if($this->lastNegotiation->isTraded() || $this->lastNegotiation->isKilled()) {
                \Log::info($attribute." >>> 5");
                return true;
            }

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
                    Last negotiation Unset -- allow any value
                */
                if($this->lastNegotiation->{$attribute} === null) {
                    \Log::info($attribute." >>> 6");
                    return true;
                }
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
                    ($this->request->input($attribute) > $this->lastNegotiation->getLatest($attribute)) == $valid &&
                    ($this->request->input($attribute) < $this->lastNegotiation->getLatest($attribute)) == !$valid
                ) {
                    \Log::info($attribute." >>> 7");
                    return true;
                } else {
                    // if the last one was an FOK & killed
                    if($this->lastNegotiation->is_killed) {
                        $cond_att = $this->lastNegotiation->cond_fok_apply_bid ? 'bid' : 'offer';
                        // if the value was not improved, yet the same and the inverse on the last killed FoK is the one under validation, ignore its value...
                        // still validate the same
                        if($inverse == $cond_att && floatval($this->request->input($attribute)) === floatval($this->lastNegotiation->getLatest($attribute))) {
                            \Log::info($attribute." >>> 8");
                            return true;
                        }
                    }
                    /*
                        Only Proceed if its the same as the prior value
                    */
                    if(floatval($this->request->input($attribute)) !== floatval($this->lastNegotiation->getLatest($attribute))) {
                        \Log::info($attribute." >>> 9");
                        return false;
                    }
                    /*
                        Last negotiation Unset -- allow any value
                    */
                    if($this->lastNegotiation->{$inverse} === null) {
                        \Log::info($attribute." >>> 10");
                        return true;
                    }
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
                        ($this->request->input($inverse) > $this->lastNegotiation->getLatest($inverse)) == !$valid &&
                        ($this->request->input($inverse) < $this->lastNegotiation->getLatest($inverse)) == $valid
                    ) {
                        \Log::info($attribute." >>> 11");
                        return true;
                    }
                }
            } else {
                \Log::info($attribute." >>> 12");
                return $this->request->input($inverse) != null;
            }
            /*
                If Value not present, Ensure the Inverse value is Present && improved

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
                $this->request->input($attribute) == null &&
                ($this->request->input($inverse) > $this->lastNegotiation->getLatest($inverse)) == !$valid &&
                ($this->request->input($inverse) < $this->lastNegotiation->getLatest($inverse)) == $valid
            ) {
                \Log::info($attribute." >>> 13");
                return true;
            }
        }
        \Log::info($attribute." >>> 14");
        return false;
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
