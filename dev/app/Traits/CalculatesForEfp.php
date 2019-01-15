<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalculatesForEfp {
	
    public function efpTwo()
    {
        $this->load(['futureGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $closingspotref =  floatval($this->futureGroups[0]->getOpVal('Spot'));

        $marketNegotiation = $this->tradeNegotiation->marketNegotiation;
        $Points = $this->tradeNegotiation->getRoot()->is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;
        
        $future1 = $closingspotref + $Points;

        $isOffer = $this->futureGroups[0]->getOpVal('is_offer',true);

        $this->load(['futureGroups']);
        
        $this->efpFees($isOffer,$is_sender, $future1, $closingspotref, $Points);
    }

    public function efpFees($isOffer,$is_sender,$future1,$FuturesSpotRef1,$points1)
    {     
    	$FutBrodirection1 = $isOffer ? 1 : -1;
    	$counterFutBrodirection1 = $FutBrodirection1 * -1;

    	$D1efpFee = config('marketmartial.confirmation_settings.efp.index.only_leg')/100;//its a percentage

    	//FUTURE = Application.Round(Future1 * D1efpFee * FutBrodirection1, 2) + Future1
    	$future =  round($future1 * $D1efpFee * $FutBrodirection1, 2) + $future1;
    	$this->futureGroups[0]->setOpVal('Future', $future,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(FuturesSpotRef1 * D1efpFee * FutureBrodirection1, 2) + (FuturesSpotRef1 + points1)
        $futureCounter =  round( ($FuturesSpotRef1 * $D1efpFee * $counterFutBrodirection1) + ($FuturesSpotRef1 + $points1), 2);
        $this->futureGroups[0]->setOpVal('Future', $futureCounter,!$is_sender);
    }
}