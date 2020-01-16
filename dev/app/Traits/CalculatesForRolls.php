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

        $isOffer = $this->futureGroups[0]->getOpVal('is_offer 1');

        $this->rollsFees($isOffer,$is_sender, $future2, $nearref, $Points);
    }

    public function rollsFees($isOffer,$is_sender,$future2,$RollNearFutureRef,$points1)
    {     
    	$FutBrodirection2 = $isOffer ? 1 : -1;
    	$counterFutBrodirection2 = $FutBrodirection2 * -1;

        $sender_org = $this->sendUser->organisation;
        $receiving_org = $this->recievingUser->organisation;
        $rolls_key = 'marketmartial.confirmation_settings.rolls.';

        //its a percentage        
        $D1rollFeeSender = $sender_org->resolveBrokerageFee($rolls_key.'index.far_leg_only')/100;
        $D1rollFeeReceiving = $receiving_org->resolveBrokerageFee($rolls_key.'index.far_leg_only')/100;

        //dd($future2, $D1rollFeeReceiving, $D1rollFeeSender, $is_sender, $FutBrodirection2);
    	//FUTURE = Application.Round(Future2 * D1rollFee * FutBrodirection2, 2) + Future2
        // Phase 2 - New calc is just future, fee is now split
    	$this->futureGroups[0]->setOpVal('Future 2', $future2,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(RollNearFutureRef * D1rollFee * FutureBrodirection2, 2) + (RollNearFutureRef + points1)
        // Phase 2 - New calc is just future, fee is now split
        $futureCounter = $RollNearFutureRef + $points1;
        $this->futureGroups[0]->setOpVal('Future 2', $futureCounter,!$is_sender);

        $contracts =  floatval($this->futureGroups[0]->getOpVal('Contract'));

        // Fee = |Amount| * Contracts * 10
        $totalFee = round(abs(round($future2 * ($is_sender ? $D1rollFeeSender : $D1rollFeeReceiving) * $FutBrodirection2, 2)) * $contracts * 10);
        // Calculate for the counter
        $totalFeeCounter = round(abs(round( ($RollNearFutureRef * ($is_sender ? $D1rollFeeReceiving : $D1rollFeeSender) * $counterFutBrodirection2), 2)) * $contracts * 10);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFee,$is_sender);
        //set for the counter
        $this->feeGroups[0]->setOpVal('Fee Total', $totalFeeCounter,!$is_sender);
    }
}