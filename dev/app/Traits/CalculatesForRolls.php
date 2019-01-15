<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalculatesForRolls {
    
    public function rollsTwo()
    {
        $this->load(['futureGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $nearref =  floatval($this->futureGroups[0]->getOpVal('Future 1'));

        $marketNegotiation = $this->tradeNegotiation->marketNegotiation;
        $Points = $this->tradeNegotiation->getRoot()->is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;
        
        $future2 = $nearref + $Points;

        $isOffer = $this->futureGroups[0]->getOpVal('is_offer 2');

        $this->rollsFees($isOffer,$is_sender, $future2, $nearref, $Points);
    }

    public function rollsFees($isOffer,$is_sender,$future2,$RollNearFutureRef,$points1)
    {     
    	$FutBrodirection2 = $isOffer ? 1 : -1;
    	$counterFutBrodirection2 = $FutBrodirection2 * -1;

    	$D1rollFee = config('marketmartial.confirmation_settings.rolls.index.far_leg_only')/100;//its a percentage

    	//FUTURE = Application.Round(Future2 * D1rollFee * FutBrodirection2, 2) + Future2
    	$future =  round($future2 * $D1rollFee * $FutBrodirection2, 2) + $future2;
    	$this->futureGroups[0]->setOpVal('Future 2', $future,!$is_sender);

        //set for the counter
        // FUTURE = Application.Round(RollNearFutureRef * D1rollFee * FutureBrodirection2, 2) + (RollNearFutureRef + points1)
        $futureCounter =  round( ($RollNearFutureRef * $D1rollFee * $counterFutBrodirection2) + ($RollNearFutureRef + $points1), 2);
        $this->futureGroups[0]->setOpVal('Future 2', $futureCounter,$is_sender);
    }
}