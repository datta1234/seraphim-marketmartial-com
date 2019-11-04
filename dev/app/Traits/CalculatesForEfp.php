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

        $this->load(['futureGroups','feeGroups']);
        
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

    	// FUTURE = Application.Round(Future1 * D1efpFee * FutBrodirection1, 2) + Future1
        // Phase 2 - New calc is just future, fee is now split
    	$this->futureGroups[0]->setOpVal('Future', $future1,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(FuturesSpotRef1 * D1efpFee * FutureBrodirection1, 2) + (FuturesSpotRef1 + points1)
        // Phase 2 - New calc is just future, fee is now split
        $futureCounter = $FuturesSpotRef1 + $points1;
        $this->futureGroups[0]->setOpVal('Future', $futureCounter,!$is_sender);

        $contracts =  floatval($this->futureGroups[0]->getOpVal('Contract'));

        // Fee = |Amount| * Contracts * 10
        $totalFee = round(abs(round($future1 * ($is_sender ? $D1efpFeeSender : $D1efpFeeReceiving) * $FutBrodirection1, 2)) * $contracts * 10);
        // Calculate for the counter
        $totalFeeCounter = round(abs(round( ($FuturesSpotRef1 * ($is_sender ? $D1efpFeeReceiving : $D1efpFeeSender) * $counterFutBrodirection1), 2)) * $contracts * 10);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFee,$is_sender);
        //set for the counter
        $this->feeGroups[0]->setOpVal('Fee Total', $totalFeeCounter,!$is_sender);
    }
}