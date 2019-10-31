<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalculatesForOptionSwitch {
    
    public function optionSwitchTwo()
    {
        $this->load(['futureGroups','optionGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $singleStock1 = $this->optionGroups[0]->userMarketRequestGroup->tradable->isStock();
        $singleStock2 = $this->optionGroups[1]->userMarketRequestGroup->tradable->isStock();
        
        if($singleStock1) {
            $SpotRef1 = floatval($this->futureGroups[0]->getOpVal('Spot'));
            $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');
            
            $val1 = round( $nominal1 / ($SpotRef1 *100), 0);
            if($val1 === 0.0) {
                // handle cant process, spotref too high
                throw new \App\Exceptions\SpotRefTooHighException("Spot Ref Too High",0);
            }

            $this->optionGroups[0]->setOpVal('Contract', $val1);
        }

        if($singleStock2) {
            $SpotRef2 = floatval($this->futureGroups[1]->getOpVal('Spot'));
            $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');

            $val2 = round( $nominal2 / ($SpotRef2 *100), 0);
            if($val2 === 0.0) {
                // handle cant process, spotref too high
                throw new \App\Exceptions\SpotRefTooHighException("Spot Ref Too High",1);
            }

            $this->optionGroups[1]->setOpVal('Contract', $val2);
        }

        $future1 =  floatval($this->futureGroups[0]->getOpVal('Future'));
        $contracts1 =  floatval($this->optionGroups[0]->getOpVal('Contract'));
        $expiry1 = Carbon::createFromFormat("Y-m-d",$this->optionGroups[0]->getOpVal('Expiration Date'));
        $strike1 =  floatval($this->optionGroups[0]->getOpVal('strike'));
        $volatility1 = ( floatval($this->optionGroups[0]->getOpVal('volatility'))/100);//its a percentage

        $future2 =  floatval($this->futureGroups[1]->getOpVal('Future'));
        $contracts2 =  floatval($this->optionGroups[1]->getOpVal('Contract'));
        $expiry2 = Carbon::createFromFormat("Y-m-d",$this->optionGroups[1]->getOpVal('Expiration Date'));
        $strike2 =  floatval($this->optionGroups[1]->getOpVal('strike'));
        $volatility2 = ( floatval($this->optionGroups[1]->getOpVal('volatility'))/100);//its a percentage 

        $future_contracts2 = null;
        $future_contracts1 = null;
        
        $is_offer1 = $this->optionGroups[0]->getOpVal('is_offer',true);
        $is_offer2 = $this->optionGroups[1]->getOpVal('is_offer',true);

        if($is_offer1 == 1) {
            $putDirection1	= 1;
            $callDirection1 = 1;
            
            $putDirection2 	= -1;
            $callDirection2 = -1; 
        } else {
            $putDirection1   = -1;
            $callDirection1  = -1;
            
            $putDirection2   = 1;
            $callDirection2  = 1;  
        }

        $startDate = Carbon::now()->startOfDay();
        
        $POD1 = round($this->putOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1, 0)  * $putDirection1;
        $COD1 = round($this->callOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1, 0) * $callDirection1;

        $POD2 = round($this->putOptionDelta($startDate,$expiry2,$future2,$strike2,$volatility2) * $contracts2, 0)  * $putDirection2;
        $COD2 = round($this->callOptionDelta($startDate,$expiry2,$future2,$strike2,$volatility2) * $contracts2, 0) * $callDirection2;

        // Leg1 - 1st Expiry
        if(abs($POD1) <= abs($COD1)) {
            //set the cell to a put
            $this->optionGroups[0]->setOpVal('is_put',true);
            $gross_prem1 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock1);
            $this->optionGroups[0]->setOpVal('Gross Premiums',$gross_prem1,$is_sender);

            $future_contracts1 = $POD1;
        } else {
           $this->optionGroups[0]->setOpVal('is_put',false);
           $gross_prem1 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock1);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem1,$is_sender);

          $future_contracts1/*cell(21,6)*/ = $COD1;
        }

        // futures and deltas buy/sell
        $isFututeOffer1 = !($future_contracts1 < 0);
        $this->futureGroups[0]->setOpVal('is_offer', $isFututeOffer1, true);
        $this->futureGroups[0]->setOpVal('is_offer', !$isFututeOffer1, false);

        // Leg2 - 2nd Expiry
        if(abs($POD2) <= abs($COD2)) {
            //set the cell to a put
            $this->optionGroups[1]->setOpVal('is_put',true);
            $gross_prem2 = $this->putOptionPremium($startDate,$expiry2,$future2,$strike2,$volatility2,$singleStock2);
            $this->optionGroups[1]->setOpVal('Gross Premiums',$gross_prem2,$is_sender);

            $future_contracts2 = $POD2;
        } else {
           $this->optionGroups[1]->setOpVal('is_put',false);
           $gross_prem2 = $this->callOptionPremium($startDate,$expiry2,$future2,$strike2,$volatility2,$singleStock2);
           $this->optionGroups[1]->setOpVal('Gross Premiums', $gross_prem2,$is_sender);

          $future_contracts2/*cell(21,6)*/ = $COD2;
        }

        // futures and deltas buy/sell
        $isFutureOffer2 = !($future_contracts2 < 0);
        $this->futureGroups[1]->setOpVal('is_offer', $isFutureOffer2, true);
        $this->futureGroups[1]->setOpVal('is_offer', !$isFutureOffer2, false);

        $this->futureGroups[0]->setOpVal('Contract', abs($future_contracts1));
        $this->futureGroups[1]->setOpVal('Contract', abs($future_contracts2));

        $this->load(['futureGroups','optionGroups','feeGroups']);

        $this->optionSwitchFees($is_offer1, $is_offer2, $gross_prem1, $gross_prem2, $is_sender, $contracts1, $contracts2,$singleStock1,$singleStock2);
    }

    public function optionSwitchFees($isOffer1,$isOffer2,$gross_prem1,$gross_prem2,$is_sender,$contracts1,$contracts2,$singleStock1,$singleStock2)
    {     
		$Brodirection1 = $isOffer1 ? 1 : -1;
        $Brodirection2 = $isOffer2 ? 1 : -1;
        $counterBrodirection1 = $Brodirection1 * -1;
        $counterBrodirection2 = $Brodirection2 * -1;

        $sender_org = $this->sendUser->organisation;
        $receiving_org = $this->recievingUser->organisation;
        $option_switch_key = 'marketmartial.confirmation_settings.option_switch.';

        //its a percentage        
        $SINGLEoptionswitchFEESender = $sender_org->resolveBrokerageFee($option_switch_key.'singles.per_leg')/100;
        $SINGLEoptionswitchFEEReceiving = $receiving_org->resolveBrokerageFee($option_switch_key.'singles.per_leg')/100;

        $IXoptionswitchFEESender = $sender_org->resolveBrokerageFee($option_switch_key.'index.per_leg')/100;
        $IXoptionswitchFEEReceiving = $receiving_org->resolveBrokerageFee($option_switch_key.'index.per_leg')/100;
        

    	// Leg1 Top40, DTop, DCap or Single?
    	if($singleStock1) {
            $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');

    		//NETPREM = Application.Round(nominal1 * SINGLEoptionswitchFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
    		$netPremium1 =  round($nominal1 * ($is_sender ? $SINGLEoptionswitchFEESender : $SINGLEoptionswitchFEEReceiving) / $contracts1 * $Brodirection1 + $gross_prem1, 2);
    		//set for the counter
    		$netPremiumCounter1 = round($nominal1 * ($is_sender ? $SINGLEoptionswitchFEEReceiving : $SINGLEoptionswitchFEESender) / $contracts1 * $counterBrodirection1 + $gross_prem1, 2);
    	} else {
			$SpotReferencePrice1 = $this->marketRequest->userMarketRequestTradables[0]->market->spot_price_ref;

    		//NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXoptionswitchFEE * Brodirection1, 0) + GrossPrem1
    		$netPremium1 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXoptionswitchFEESender : $IXoptionswitchFEEReceiving) * $Brodirection1) + $gross_prem1;
    		//set for the counter
    		$netPremiumCounter1 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXoptionswitchFEEReceiving : $IXoptionswitchFEESender) * $counterBrodirection1) + $gross_prem1;
    	}

    	// Leg2 Top40, DTop, DCap or Single?
    	if($singleStock2) {
            $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');

    		//NETPREM = Application.Round(nominal2 * SINGLEoptionswitchFEE / Contracts2 * Brodirection2 + GrossPrem2, 2)
    		$netPremium2 =  round($nominal2 * ($is_sender ? $SINGLEoptionswitchFEESender : $SINGLEoptionswitchFEEReceiving) / $contracts2 * $Brodirection2 + $gross_prem2, 2);
    		//set for the counter
    		$netPremiumCounter2 =  round($nominal2 * ($is_sender ? $SINGLEoptionswitchFEEReceiving : $SINGLEoptionswitchFEESender) / $contracts2 * $counterBrodirection2 + $gross_prem2, 2);
    	} else {
    		$SpotReferencePrice2 = $this->marketRequest->userMarketRequestTradables[1]->market->spot_price_ref;

    		//NETPREM = Application.RoundDown(SpotReferencePrice2 * 10 * IXoptionswitchFEE * Brodirection2, 0) + GrossPrem2
    		$netPremium2 =  floor($SpotReferencePrice2 * 10 * ($is_sender ? $IXoptionswitchFEESender : $IXoptionswitchFEEReceiving) * $Brodirection2) + $gross_prem2;
    		//set for the counter
    		$netPremiumCounter2 =  floor($SpotReferencePrice2 * 10 * ($is_sender ? $IXoptionswitchFEEReceiving : $IXoptionswitchFEESender) * $counterBrodirection2) + $gross_prem2;
    	}

        // Fee = |GrossPrem - NetPremContracts| * Contracts
        $fee1 = abs($gross_prem1 - $netPremium1) * $contracts1;
        $fee2 = abs($gross_prem2 - $netPremium2) * $contracts2;
        // set for the counter
        $feeCounter1 = abs($gross_prem1 - $netPremiumCounter1) * $contracts1;
        $feeCounter2 = abs($gross_prem2 - $netPremiumCounter2) * $contracts2;
        // Fee Total = SUM(Fee)
        $totalFee = round($fee1 + $fee2);
        $totalFeeCounter = round($feeCounter1 + $feeCounter2);

    	$this->optionGroups[0]->setOpVal('Net Premiums', $netPremium1,$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremium2,$is_sender);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFee,$is_sender);

        //set for the counter
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremiumCounter1,!$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremiumCounter2,!$is_sender);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFeeCounter,!$is_sender);
    }
}