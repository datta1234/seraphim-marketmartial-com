<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalculatesForEfpSwitch {
    
    public function efpSwitchTwo()
    {
        $this->load(['futureGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $closingspotref1 =  floatval($this->futureGroups[0]->getOpVal('Spot'));
        $closingspotref2 =  floatval($this->futureGroups[1]->getOpVal('Spot'));

        $marketNegotiation = $this->tradeNegotiation->marketNegotiation;
        $tradeNegotiationPoints = $this->tradeNegotiation->getRoot()->is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;

        $points1 = $this->futureGroups[0]->userMarketRequestGroup->is_selected ? $this->futureGroups[0]->userMarketRequestGroup->volatility->volatility : $tradeNegotiationPoints;
        $points2 = $this->futureGroups[1]->userMarketRequestGroup->is_selected ? $this->futureGroups[0]->userMarketRequestGroup->volatility->volatility : $tradeNegotiationPoints;

        $future1 = $closingspotref1 + $points1;
        $future2 = $closingspotref2 + $points2;
        
        $future_contracts/*cell(21,6)*/ = $this->quantity;

        $isOffer1 = $this->futureGroups[0]->getOpVal('is_offer',true);
        $isOffer2 = $this->futureGroups[0]->getOpVal('is_offer',true);

        $this->load(['futureGroups']);

        $this->efpSwitchFees($isOffer1, $isOffer2, $is_sender, $future1, $future2, $closingspotref1, $closingspotref2, $points1, $points2);
    }

    public function efpSwitchFees($isOffer1,$isOffer2,$is_sender,$future1,$future2,$FuturesSpotRef1,$FuturesSpotRef2,$points1,$points2)
    {     
    	$FutBrodirection1 = $isOffer1 ? 1 : -1;
        $FutBrodirection2 = $isOffer2 ? 1 : -1;
    	$counterFutBrodirection1 = $FutBrodirection1 * -1;
        $counterFutBrodirection2 = $FutBrodirection2 * -1;

    	$D1switchFEE = config('marketmartial.confirmation_settings.efp_switch.index.per_leg')/100;//its a percentage

    	//FUTURE = Application.Round(Future1 * D1switchFEE * FutBrodirection1, 2) + Future1
    	$finalFuture1 =  round($future1 * $D1switchFEE * $FutBrodirection1, 2) + $future1;
        $finalFuture2 =  round($future2 * $D1switchFEE * $FutBrodirection2, 2) + $future2;
    	$this->futureGroups[0]->setOpVal('Future', $finalFuture1,$is_sender);
        $this->futureGroups[1]->setOpVal('Future', $finalFuture2,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(FuturesSpotRef1 * D1switchFEE * FutureBrodirection1, 2) + (FuturesSpotRef1 + points1)
        $finalFutureCounter1 =  round($FuturesSpotRef1 * $D1switchFEE * $counterFutBrodirection1, 2) + ($FuturesSpotRef1 + $points1);
        $finalFutureCounter2 =  round($FuturesSpotRef2 * $D1switchFEE * $counterFutBrodirection2, 2) + ($FuturesSpotRef2 + $points2);
        $this->futureGroups[0]->setOpVal('Future', $finalFutureCounter1,!$is_sender);
        $this->futureGroups[1]->setOpVal('Future', $finalFutureCounter2,!$is_sender);
    }
}