<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForRolls {
    
    public function rollsTwo()
    {
        $this->load(['futureGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $nearref =  floatval($this->futureGroups[0]->getOpVal('Future 1'));
        $user_market_request_groups = $this->tradeNegotiation->userMarket->userMarketRequest->userMarketRequestGroups;
        
        // @TODO figure out how to get points - the agreed upon bid/offer value
        $Points = $user_market_request_groups[0]->getDynamicItem('Quantity');
        
        $future2 = $nearref + $Points;

        $isOffer = $this->futureGroups[0]->getOpVal('is_offer 2');

        $this->rollsFees($isOffer,$is_sender, $future2, $nearref, $Points);
    }

    public function rollsFees($isOffer,$is_sender,$future2,$RollNearFutureRef,$points1)
    {     
    	$FutBrodirection1 = $isOffer ? 1 : -1;
    	$counterFutBrodirection1 = $FutBrodirection1 * -1;

    	$D1rollFee = config('marketmartial.confirmation_settings.rolls.index.far_leg_only')/100;//its a percentage

    	//FUTURE = Application.Round(Future2 * D1rollFee * FutBrodirection2, 2) + Future2
    	$future =  round($future2 * $D1rollFee * $FutBrodirection1, 2) + $future2;
    	$this->optionGroups[0]->setOpVal('Future', $future,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(RollNearFutureRef * D1rollFee * FutureBrodirection2, 2) + (RollNearFutureRef + points1)
        $futureCounter =  round( ($RollNearFutureRef * $D1efpFee * $counterFutBrodirection1) + ($RollNearFutureRef + $points1), 2);
        $this->optionGroups[0]->setOpVal('Future', $futureCounter,!$is_sender);
    }
}