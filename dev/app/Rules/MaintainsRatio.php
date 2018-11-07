<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MaintainsRatio implements Rule
{
    private $request; 
    private $path; 
    private $last;
    private $ratio;
    private $message = "The Ratio must be maintained";

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request, $ratio, $last, $path = null)
    {
        $this->request = $request;
        $this->ratio = floatval($ratio);
        $this->last = $last;
        $this->path = $path;
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
        $path = '';
        if($this->path != null) {
            $path = $this->path.'.';
        }
        $bid_qty = ( $attribute == 'bid_qty' ? $value : $this->request->input($path.'bid_qty') );
        $offer_qty = ( $attribute == 'offer_qty' ? $value : $this->request->input($path.'offer_qty') );
        
        if($bid_qty == null || $bid_qty == 0) {
            if($this->last) {
                $bid_qty = $this->last->getLatestBidQty();
            } else {
                // div by 0
                $this->message = "Invalid Ratio, missing bid value";
                return false;
            }
        }
        if($offer_qty == null || $offer_qty == 0) {
            if($this->last) {
                $offer_qty = $this->last->getLatestOfferQty();
            } else {
                // div by 0
                $this->message = "Invalid Ratio, missing offer value";
                return false;
            }
        }

        $ratio = (floatval($bid_qty) / floatval($offer_qty));
        \Log::info([$ratio, $this->ratio]);
        // ratio same
        return $ratio === $this->ratio;
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
