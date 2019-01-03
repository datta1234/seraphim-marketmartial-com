<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForEfp {
	
    public function efpTwo()
    {
        $this->load(['futureGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $closingspotref =  floatval($this->futureGroups[0]->getOpVal('Spot'));
        $user_market_request_groups = $this->tradeNegotiation->userMarket->userMarketRequest->userMarketRequestGroups;
        $Points = $user_market_request_groups[0]->getDynamicItem('Quantity');
        
        $future1 = $closingspotref + $Points;
        
        $future_contracts/*cell(21,6)*/ = $this->quantity;

        $isOffer = $this->futureGroups[0]->getOpVal('is_offer');

        $this->efpFees($isOffer,$is_sender, $future1, $closingspotref, $Points);
    }

    public function efpFees($isOffer,$is_sender,$future1,$FuturesSpotRef1,$points1)
    {     
    	$FutBrodirection1 = $isOffer ? 1 : -1;
    	$counterFutBrodirection1 = $FutBrodirection1 * -1;

    	$D1efpFee = config('marketmartial.confirmation_settings.efp.index.only_leg');

    	//FUTURE = Application.Round(Future1 * D1efpFee * FutBrodirection1, 2) + Future1
    	$future =  round($future1 * $D1efpFee * $FutBrodirection1, 2) + $future1;
    	$this->optionGroups[0]->setOpVal('Future', $future,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(FuturesSpotRef1 * D1efpFee * FutureBrodirection1, 2) + (FuturesSpotRef1 + points1)
        $futureCounter =  round( ($FuturesSpotRef1 * $D1efpFee * $counterFutBrodirection1) + ($FuturesSpotRef1 + $points1), 2);
        $this->optionGroups[0]->setOpVal('Future', $futureCounter,!$is_sender);
    }
}