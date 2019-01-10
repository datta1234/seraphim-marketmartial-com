<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForEfpSwitch {
    
    public function efpSwitchTwo()
    {
        $this->load(['futureGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $closingspotref1 =  floatval($this->futureGroups[0]->getOpVal('Spot'));
        $closingspotref2 =  floatval($this->futureGroups[1]->getOpVal('Spot'));

        $user_market_request_groups = $this->tradeNegotiation->userMarket->userMarketRequest->userMarketRequestGroups;

        // @TODO figure out how to get points - depending on choice one is going to be choice value and the other the agreed upon bid/offer
        $points1 = $user_market_request_groups[0]->getDynamicItem('Quantity');
        $points2 = $user_market_request_groups[1]->getDynamicItem('Quantity');

        $future1 = $closingspotref1 + $Points;
        $future2 = $closingspotref2 + $Points;
        
        $future_contracts/*cell(21,6)*/ = $this->quantity;

        $isOffer = $this->futureGroups[0]->getOpVal('is_offer');

        $this->efpSwitchFees($isOffer,$is_sender, $future1, $future2, $closingspotref1, $closingspotref2, $points1, $points2);
    }

    public function efpSwitchFees($isOffer,$is_sender,$future1,$future2,$FuturesSpotRef1,$FuturesSpotRef2,$points1,$points2)
    {     
    	$FutBrodirection = $isOffer ? 1 : -1;
    	$counterFutBrodirection = $FutBrodirection * -1;

    	$D1switchFEE = config('marketmartial.confirmation_settings.efp_switch.index.per_leg')/100;//its a percentage

    	//FUTURE = Application.Round(Future1 * D1switchFEE * FutBrodirection1, 2) + Future1
    	$finalFuture1 =  round($future1 * $D1switchFEE * $FutBrodirection, 2) + $future1;
        $finalFuture2 =  round($future2 * $D1switchFEE * $FutBrodirection, 2) + $future2;
    	$this->optionGroups[0]->setOpVal('Future', $finalFuture1,$is_sender);
        $this->optionGroups[0]->setOpVal('Future', $finalFuture2,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(FuturesSpotRef1 * D1switchFEE * FutureBrodirection1, 2) + (FuturesSpotRef1 + points1)
        $finalFutureCounter1 =  round( ($FuturesSpotRef1 * $D1switchFEE * $counterFutBrodirection) + ($FuturesSpotRef1 + $points1), 2);
        $finalFutureCounter1 =  round( ($FuturesSpotRef2 * $D1switchFEE * $counterFutBrodirection) + ($FuturesSpotRef2 + $points2), 2);
        $this->optionGroups[0]->setOpVal('Future', $finalFutureCounter1,!$is_sender);
        $this->optionGroups[0]->setOpVal('Future', $finalFutureCounter2,!$is_sender);
    }
}