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
        $points2 = $this->futureGroups[1]->userMarketRequestGroup->is_selected ? $this->futureGroups[1]->userMarketRequestGroup->volatility->volatility : $tradeNegotiationPoints;

        $future1 = $closingspotref1 + $points1;
        $future2 = $closingspotref2 + $points2;
        
        $future_contracts/*cell(21,6)*/ = $this->quantity;

        $isOffer1 = $this->futureGroups[0]->getOpVal('is_offer',true);
        $isOffer2 = $this->futureGroups[1]->getOpVal('is_offer',true);

        $this->load(['futureGroups']);

        $this->efpSwitchFees($isOffer1, $isOffer2, $is_sender, $future1, $future2, $closingspotref1, $closingspotref2, $points1, $points2);
    }

    public function efpSwitchFees($isOffer1,$isOffer2,$is_sender,$future1,$future2,$FuturesSpotRef1,$FuturesSpotRef2,$points1,$points2)
    {     
    	$FutBrodirection1 = $isOffer1 ? 1 : -1;
        $FutBrodirection2 = $isOffer2 ? 1 : -1;
    	$counterFutBrodirection1 = $FutBrodirection1 * -1;
        $counterFutBrodirection2 = $FutBrodirection2 * -1;

        $sender_org = $this->sendUser->organisation;
        $receiving_org = $this->recievingUser->organisation;
        $efp_switch_key = 'marketmartial.confirmation_settings.efp_switch.';

        //its a percentage        
        $D1switchFEESender = $sender_org->resolveBrokerageFee($efp_switch_key.'index.per_leg')/100;
        $D1switchFEEReceiving = $receiving_org->resolveBrokerageFee($efp_switch_key.'index.per_leg')/100;

    	//FUTURE = Application.Round(Future1 * D1switchFEE * FutBrodirection1, 2) + Future1
        // Phase 2 - New calc is just future, fee is now split
    	$this->futureGroups[0]->setOpVal('Future', $future1,$is_sender);
        $this->futureGroups[1]->setOpVal('Future', $future2,$is_sender);

        //set for the counter
        // FUTURE = Application.Round(FuturesSpotRef1 * D1switchFEE * FutureBrodirection1, 2) + (FuturesSpotRef1 + points1)
        // Phase 2 - New calc is just future, fee is now split
        $finalFutureCounter1 = $FuturesSpotRef1 + $points1;
        $finalFutureCounter2 = $FuturesSpotRef2 + $points2;
        $this->futureGroups[0]->setOpVal('Future', $finalFutureCounter1,!$is_sender);
        $this->futureGroups[1]->setOpVal('Future', $finalFutureCounter2,!$is_sender);

        $contracts1 =  floatval($this->futureGroups[0]->getOpVal('Contract'));
        $contracts2 =  floatval($this->futureGroups[1]->getOpVal('Contract'));

        // Fee = |Amount| * Contracts * 10
        $fee1 = abs(round($future1 * ($is_sender ? $D1switchFEESender : $D1switchFEEReceiving) * $FutBrodirection1, 5)) * $contracts1 * 10;
        $fee2 = abs(round($future2 * ($is_sender ? $D1switchFEESender : $D1switchFEEReceiving) * $FutBrodirection2, 5)) * $contracts2 * 10;
        // set for the counter
        $feeCounter1 = abs(round($FuturesSpotRef1 * ($is_sender ? $D1switchFEEReceiving : $D1switchFEESender) * $counterFutBrodirection1, 5)) * $contracts1 * 10;
        $feeCounter2 = abs(round($FuturesSpotRef2 * ($is_sender ? $D1switchFEEReceiving : $D1switchFEESender) * $counterFutBrodirection2, 5)) * $contracts2 * 10;
        // Fee Total = SUM(Fee)
        $totalFee = round($fee1 + $fee2);
        $totalFeeCounter = round($feeCounter1 + $feeCounter2);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFee,$is_sender);
        //set for the counter
        $this->feeGroups[0]->setOpVal('Fee Total', $totalFeeCounter,!$is_sender);
    }
}