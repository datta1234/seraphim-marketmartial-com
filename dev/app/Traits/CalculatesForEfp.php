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
        
        $sender_org = $this->sendUser->organisation;
        $receiving_org = $this->recievingUser->organisation;
        $efp_key = 'marketmartial.confirmation_settings.efp.';

        //its a percentage        
        $D1efpFeeSender = $sender_org->resolveBrokerageFee($efp_key.'index.only_leg')/100;
        $D1efpFeeReceiving = $receiving_org->resolveBrokerageFee($efp_key.'index.only_leg')/100;

    	//FUTURE = Application.Round(Future1 * D1efpFee * FutBrodirection1, 2) + Future1
    	$future =  round($future1 * ($is_sender ? $D1efpFeeSender : $D1efpFeeReceiving) * $FutBrodirection1, 2) + $future1;
    	$this->futureGroups[0]->setOpVal('Future', $future,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(FuturesSpotRef1 * D1efpFee * FutureBrodirection1, 2) + (FuturesSpotRef1 + points1)
        $futureCounter =  round( ($FuturesSpotRef1 * ($is_sender ? $D1efpFeeReceiving : $D1efpFeeSender) * $counterFutBrodirection1), 2) + ($FuturesSpotRef1 + $points1);
        $this->futureGroups[0]->setOpVal('Future', $futureCounter,!$is_sender);
    }
}