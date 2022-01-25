<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalculatesForFly {
    
    public function flyTwo()
    {
        $this->load(['futureGroups','optionGroups']);
    	
        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $singleStock = $this->optionGroups[0]->userMarketRequestGroup->tradable->isStock();
        
        if($singleStock) {
            $SpotRef = floatval($this->futureGroups[0]->getOpVal('Spot'));
            $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');
            $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');
            $nominal3 = $this->optionGroups[2]->getOpVal('Nominal');
            
            $val1 = round( $nominal1 / ($SpotRef * 100), 0);
            $val2 = round( $nominal2 / ($SpotRef * 100), 0);
            $val3 = round( $nominal3 / ($SpotRef * 100), 0);
            if($val1 === 0.0 || $val2 === 0.0 || $val3 === 0.0) {
                // handle cant process, spotref too high
                throw new \App\Exceptions\SpotRefTooHighException("Spot Ref Too High",0);
            }

            $this->optionGroups[0]->setOpVal('Contract', $val1);
            $this->optionGroups[1]->setOpVal('Contract', $val2);
            $this->optionGroups[2]->setOpVal('Contract', $val3);
        }

        $future1 =  floatval($this->futureGroups[0]->getOpVal('Future'));
        $contracts1 =  floatval($this->optionGroups[0]->getOpVal('Contract'));
        $expiry1 = Carbon::createFromFormat("Y-m-d",$this->optionGroups[0]->getOpVal('Expiration Date'));
        $strike1 =  floatval($this->optionGroups[0]->getOpVal('strike'));
        $volatility1 = ( floatval($this->optionGroups[0]->getOpVal('volatility'))/100);//its a percentage

        $contracts2 =  floatval($this->optionGroups[1]->getOpVal('Contract'));
        $strike2 =  floatval($this->optionGroups[1]->getOpVal('strike'));
        $volatility2 = ( floatval($this->optionGroups[1]->getOpVal('volatility'))/100);//its a percentage

        $contracts3 =  floatval($this->optionGroups[2]->getOpVal('Contract'));
        $strike3 =  floatval($this->optionGroups[2]->getOpVal('strike'));
        $volatility3 = ( floatval($this->optionGroups[2]->getOpVal('volatility'))/100);//its a percentage
    	
        $is_offer1 = $this->optionGroups[0]->getOpVal('is_offer',true);
        $is_offer2 = $this->optionGroups[1]->getOpVal('is_offer',true);
        $is_offer3 = $this->optionGroups[2]->getOpVal('is_offer',true);

        $future_contracts = null;        

        if($is_offer1 == 1) {
            $putDirection1	= 1;
            $callDirection1 = 1;
            
            $putDirection2 	= -1;
            $callDirection2 = -1;

            $putDirection3 	= 1;
            $callDirection3 = 1; 
        } else {
            $putDirection1   = -1;
            $callDirection1  = -1;
            
            $putDirection2   = 1;
            $callDirection2  = 1;

            $putDirection3   = -1;
            $callDirection3  = -1; 
        }

        $startDate = Carbon::now()->startOfDay();
        
        $POD1 = round($this->putOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1, 0)  * $putDirection1;
        $COD1 = round($this->callOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1, 0) * $callDirection1;

        $POD2 = round($this->putOptionDelta($startDate,$expiry1,$future1,$strike2,$volatility2) * $contracts2, 0)  * $putDirection2;
        $COD2 = round($this->callOptionDelta($startDate,$expiry1,$future1,$strike2,$volatility2) * $contracts2, 0) * $callDirection2;

        $POD3 = round($this->putOptionDelta($startDate,$expiry1,$future1,$strike3,$volatility3) * $contracts3, 0)  * $putDirection3;
        $COD3 = round($this->callOptionDelta($startDate,$expiry1,$future1,$strike3,$volatility3) * $contracts3, 0) * $callDirection3;

        /*
            Phase 3 update as requested by client
            Only use prefered Option Delta 
                i.e. abs($POD1) <= abs($COD1)
            On the initial calc
                i.e. Gross Premium not set
            After this point the put / call will dictate the set
                i.e. is_put true / false
        */
        $gross_prem1_exists = !is_null($this->optionGroups[0]->getOpVal('Gross Premiums'));
        $gross_prem2_exists = !is_null($this->optionGroups[1]->getOpVal('Gross Premiums'));
        $gross_prem3_exists = !is_null($this->optionGroups[2]->getOpVal('Gross Premiums'));

        $pref_option_premium1 = $pref_option_premium2 = $pref_option_premium3 = abs($POD1 + $POD2 + $POD3) <= abs($COD1 + $COD2 + $COD3);

        if($gross_prem1_exists && $gross_prem2_exists && $gross_prem3_exists) {
            $pref_option_premium1 = $this->optionGroups[0]->getOpVal('is_put');
            $pref_option_premium2 = $this->optionGroups[1]->getOpVal('is_put');
            $pref_option_premium3 = $this->optionGroups[2]->getOpVal('is_put');
        }

        $future_contracts = 0;

        if($pref_option_premium1) {
            //set the cell to a put
            $this->optionGroups[0]->setOpVal('is_put',true);
            $gross_prem1 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
            $this->optionGroups[0]->setOpVal('Gross Premiums',$gross_prem1,$is_sender);
            $future_contracts += $POD1;
        } else {
            $this->optionGroups[0]->setOpVal('is_put',false);
            $gross_prem1 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
            $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem1,$is_sender);
            $future_contracts += $COD1;
        }

        if($pref_option_premium2) {
            //set the cell to a put
            $this->optionGroups[1]->setOpVal('is_put',true);
            $gross_prem2 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike2,$volatility2,$singleStock);
            $this->optionGroups[1]->setOpVal('Gross Premiums',$gross_prem2,$is_sender);
            $future_contracts += $POD2;
        } else {
            $this->optionGroups[1]->setOpVal('is_put',false);
            $gross_prem2 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike2,$volatility2,$singleStock);
            $this->optionGroups[1]->setOpVal('Gross Premiums', $gross_prem2,$is_sender);
            $future_contracts += $COD2;
        }

        if($pref_option_premium3) {
            //set the cell to a put
            $this->optionGroups[2]->setOpVal('is_put',true);
            $gross_prem3 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike3,$volatility3,$singleStock);
            $this->optionGroups[2]->setOpVal('Gross Premiums',$gross_prem3,$is_sender);
            $future_contracts += $POD3;
        } else {
            $this->optionGroups[2]->setOpVal('is_put',false);
            $gross_prem3 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike3,$volatility3,$singleStock);
            $this->optionGroups[2]->setOpVal('Gross Premiums', $gross_prem3,$is_sender);
            $future_contracts += $COD3;
        }
        
        // futures and deltas buy/sell
        $isFututeOffer = !($future_contracts < 0);
        $this->futureGroups[0]->setOpVal('is_offer', $isFututeOffer, true);
        $this->futureGroups[0]->setOpVal('is_offer', !$isFututeOffer, false);

        $this->futureGroups[0]->setOpVal('Contract', abs($future_contracts));

        $this->load(['futureGroups','optionGroups','feeGroups']);

        $this->flyFees($is_offer1, $is_offer2, $is_offer3, $gross_prem1, $gross_prem2, $gross_prem3, $is_sender, $contracts1, $contracts2, $contracts3,$singleStock);
    }

    public function flyFees($isOffer1,$isOffer2,$isOffer3,$gross_prem1,$gross_prem2,$gross_prem3,$is_sender,$contracts1,$contracts2,$contracts3,$singleStock)
    {     
    	$Brodirection1 = $isOffer1 ? 1 : -1;
        $Brodirection2 = $isOffer2 ? 1 : -1;
        $Brodirection3 = $isOffer3 ? 1 : -1;
        $counterBrodirection1 = $Brodirection1 * -1;
        $counterBrodirection2 = $Brodirection2 * -1;
        $counterBrodirection3 = $Brodirection3 * -1;

        $sender_org = $this->sendUser->organisation;
        $receiving_org = $this->recievingUser->organisation;
        $fly_key = 'marketmartial.confirmation_settings.fly.';
            
        if($singleStock) {
            //its a percentage        
            $SINGLEflyFEESender = $sender_org->resolveBrokerageFee($fly_key.'singles.per_leg')/100;
            $SINGLEflyFEEReceiving = $receiving_org->resolveBrokerageFee($fly_key.'singles.per_leg')/100;
            
            $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');
            $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');
            $nominal3 = $this->optionGroups[2]->getOpVal('Nominal');

            // NETPREM = Round(nominal1 * SINGLEflyFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
            $netPremium1 =  round($nominal1 * ($is_sender ? $SINGLEflyFEESender : $SINGLEflyFEEReceiving) / $contracts1 * $Brodirection1 + $gross_prem1, 2);
            $netPremium2 =  round($nominal2 * ($is_sender ? $SINGLEflyFEESender : $SINGLEflyFEEReceiving) / $contracts2 * $Brodirection2 + $gross_prem2, 2);
            $netPremium3 =  round($nominal3 * ($is_sender ? $SINGLEflyFEESender : $SINGLEflyFEEReceiving) / $contracts3 * $Brodirection3 + $gross_prem3, 2);
            //set for the counter
            $netPremiumCounter1 =  round($nominal1 * ($is_sender ? $SINGLEflyFEEReceiving : $SINGLEflyFEESender) / $contracts1 * $counterBrodirection1 + $gross_prem1, 2);
            $netPremiumCounter2 =  round($nominal2 * ($is_sender ? $SINGLEflyFEEReceiving : $SINGLEflyFEESender) / $contracts2 * $counterBrodirection2 + $gross_prem2, 2);
            $netPremiumCounter3 =  round($nominal3 * ($is_sender ? $SINGLEflyFEEReceiving : $SINGLEflyFEESender) / $contracts3 * $counterBrodirection3 + $gross_prem3, 2);
        } else {
            //its a percentage        
            $IXflyFEESender = $sender_org->resolveBrokerageFee($fly_key.'index.per_leg')/100;
            $IXflyFEEReceiving = $receiving_org->resolveBrokerageFee($fly_key.'index.per_leg')/100;

            //get the spot price ref.
            $SpotReferencePrice1 = $this->marketRequest->userMarketRequestTradables[0]->market->spot_price_ref;

            // NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXflyFEE * Brodirection1, 0) + GrossPrem1
            $netPremium1 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXflyFEESender : $IXflyFEEReceiving) * $Brodirection1) + $gross_prem1;
            $netPremium2 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXflyFEESender : $IXflyFEEReceiving) * $Brodirection2) + $gross_prem2; 
            $netPremium3 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXflyFEESender : $IXflyFEEReceiving) * $Brodirection3) + $gross_prem3; 
            //set for the counter
            $netPremiumCounter1 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXflyFEEReceiving : $IXflyFEESender) * $counterBrodirection1) + $gross_prem1;
            $netPremiumCounter2 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXflyFEEReceiving : $IXflyFEESender) * $counterBrodirection2) + $gross_prem2;
            $netPremiumCounter3 =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXflyFEEReceiving : $IXflyFEESender) * $counterBrodirection3) + $gross_prem3;
        }

        // Fee = |GrossPrem - NetPremContracts| * Contracts
        $fee1 = abs($gross_prem1 - $netPremium1) * $contracts1;
        $fee2 = abs($gross_prem2 - $netPremium2) * $contracts2;
        $fee3 = abs($gross_prem3 - $netPremium3) * $contracts3;
        // set for the counter
        $feeCounter1 = abs($gross_prem1 - $netPremiumCounter1) * $contracts1;
        $feeCounter2 = abs($gross_prem2 - $netPremiumCounter2) * $contracts2;
        $feeCounter3 = abs($gross_prem3 - $netPremiumCounter3) * $contracts3;
        // Fee Total = SUM(Fee)
        $totalFee = round($fee1 + $fee2 + $fee3);
        $totalFeeCounter = round($feeCounter1 + $feeCounter2 + $feeCounter3);

        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium1,$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremium2,$is_sender);
        $this->optionGroups[2]->setOpVal('Net Premiums', $netPremium3,$is_sender);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFee,$is_sender);
        
        //set for the counter
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremiumCounter1,!$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremiumCounter2,!$is_sender);
        $this->optionGroups[2]->setOpVal('Net Premiums', $netPremiumCounter3,!$is_sender);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFeeCounter,!$is_sender);
    }
}