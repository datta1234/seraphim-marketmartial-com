<?php

namespace App\Traits;
use Carbon\Carbon;
use App\Models\UserManagement\BrokerageFee;

trait CalculatesForCalendar {
	
    public function calendarTwo()
    {
        $this->load(['futureGroups','optionGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $singleStock = $this->optionGroups[0]->userMarketRequestGroup->tradable->isStock();
        $SpotRef = null;
        
        if($singleStock) {
            $SpotRef = floatval($this->futureGroups[0]->getOpVal('Spot'));
            $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');
            $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');
            
            $val1 = round( $nominal1 / ($SpotRef * 100), 0);
            $val2 = round( $nominal2 / ($SpotRef * 100), 0);
            if($val1 === 0.0 || $val2 === 0.0) {
                // handle cant process, spotref too high
                throw new \App\Exceptions\SpotRefTooHighException("Spot Ref Too High",0);
            }

            $this->optionGroups[0]->setOpVal('Contract', $val1);
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

        $future_contracts1  = null;
        $future_contracts2  = null;
        
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

        $pref_option_premium1 = abs($POD1) <= abs($COD1);
        $pref_option_premium2 = abs($POD2) <= abs($COD2);

        if($gross_prem1_exists && $gross_prem2_exists) {
            $pref_option_premium1 = $this->optionGroups[0]->getOpVal('is_put');
            $pref_option_premium2 = $this->optionGroups[1]->getOpVal('is_put');
        }

        // Leg1 - 1st Expiry
        if($pref_option_premium1) {
            //set the cell to a put
            $this->optionGroups[0]->setOpVal('is_put',true);
            $gross_prem1 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
            $this->optionGroups[0]->setOpVal('Gross Premiums',$gross_prem1,$is_sender);

            $future_contracts1 = $POD1;
        } else {
           $this->optionGroups[0]->setOpVal('is_put',false);
           $gross_prem1 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem1,$is_sender);

          $future_contracts1/*cell(21,6)*/ = $COD1;
        }

        // futures and deltas buy/sell
        $isFututeOffer1 = !($future_contracts1 < 0);
        $this->futureGroups[0]->setOpVal('is_offer', $isFututeOffer1, true);
        $this->futureGroups[0]->setOpVal('is_offer', !$isFututeOffer1, false);

        // Leg2 - 2nd Expiry
        if($pref_option_premium2) {
            //set the cell to a put
            $this->optionGroups[1]->setOpVal('is_put',true);
            $gross_prem2 = $this->putOptionPremium($startDate,$expiry2,$future2,$strike2,$volatility2,$singleStock);
            $this->optionGroups[1]->setOpVal('Gross Premiums',$gross_prem2,$is_sender);

            $future_contracts2 = $POD2;
        } else {
           $this->optionGroups[1]->setOpVal('is_put',false);
           $gross_prem2 = $this->callOptionPremium($startDate,$expiry2,$future2,$strike2,$volatility2,$singleStock);
           $this->optionGroups[1]->setOpVal('Gross Premiums', $gross_prem2,$is_sender);

          $future_contracts2/*cell(21,6)*/ = $COD2;
        }

        // futures and deltas buy/sell
        $isFutureOffer2 = !($future_contracts2 < 0);
        $this->futureGroups[1]->setOpVal('is_offer', $isFutureOffer2, true);
        $this->futureGroups[1]->setOpVal('is_offer', !$isFutureOffer2, false);

        //dd($future_contracts1,$future_contracts2);
        $this->futureGroups[0]->setOpVal('Contract', abs($future_contracts1));
        $this->futureGroups[1]->setOpVal('Contract', abs($future_contracts2));

        $this->load(['futureGroups','optionGroups','feeGroups']);

        $this->calendarFees($is_offer1, $is_offer2, $gross_prem1, $gross_prem2, $is_sender, $contracts1, $contracts2, $singleStock, $SpotRef);
    }

    public function calendarFees($isOffer1,$isOffer2,$gross_prem1,$gross_prem2,$is_sender,$contracts1,$contracts2,$singleStock, $SpotRef)
    {   
        $Brodirection1 = $isOffer1 ? 1 : -1;
        $Brodirection2 = $isOffer2 ? 1 : -1;
        $counterBrodirection1 = $Brodirection1 * -1;
        $counterBrodirection2 = $Brodirection2 * -1;

        $sender_org = $this->sendUser->organisation;
        $receiving_org = $this->recievingUser->organisation;
        $calendar_key = 'marketmartial.confirmation_settings.calendar.';

        if($singleStock) {
            // These are percentage values
            $SINGLEcalendarbigFEESender = $sender_org->resolveBrokerageFee($calendar_key.'singles.big_leg')/100;
            $SINGLEcalendarbigFEEReceiving = $receiving_org->resolveBrokerageFee($calendar_key.'singles.big_leg')/100;

            $SINGLEcalendarsmallFEESender = $sender_org->resolveBrokerageFee($calendar_key.'singles.small_leg')/100;
            $SINGLEcalendarsmallFEEReceiving = $receiving_org->resolveBrokerageFee($calendar_key.'singles.small_leg')/100;
            
            $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');
            $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');
            if($nominal1 < $nominal2) {
                // NETPREM = Round(nominal1 * SINGLEcalendarbigFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
                $netPremium1 =  round($nominal1 * ($is_sender ? $SINGLEcalendarbigFEESender : $SINGLEcalendarbigFEEReceiving) / $contracts1 * $Brodirection1 + $gross_prem1, 2);
                // NETPREM = Round(nominal2 * SINGLEcalendarsmallFEE / Contracts2 * Brodirection2 + GrossPrem2, 2)
                $netPremium2 =  round($nominal2 * ($is_sender ? $SINGLEcalendarsmallFEESender : $SINGLEcalendarsmallFEEReceiving) / $contracts2 * $Brodirection2 + $gross_prem2, 2);
                
                //set for the counter
                $netPremiumCounter1 =  round($nominal1 * ($is_sender ? $SINGLEcalendarbigFEEReceiving : $SINGLEcalendarbigFEESender) / $contracts1 * $counterBrodirection1 + $gross_prem1, 2);
                $netPremiumCounter2 =  round($nominal2 * ($is_sender ? $SINGLEcalendarsmallFEEReceiving : $SINGLEcalendarsmallFEESender) / $contracts2 * $counterBrodirection2 + $gross_prem2, 2);
            } else {
                // NETPREM = Round(nominal1 * SINGLEcalendarsmallFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
                $netPremium1 =  round($nominal1 * ($is_sender ? $SINGLEcalendarsmallFEESender : $SINGLEcalendarsmallFEEReceiving) / $contracts1 * $Brodirection1 + $gross_prem1, 2);
                // NETPREM = Round(nominal2 * SINGLEcalendarbigFEE / Contracts2 * Brodirection2 + GrossPrem2, 2)
                $netPremium2 =  round($nominal2 * ($is_sender ? $SINGLEcalendarbigFEESender : $SINGLEcalendarbigFEEReceiving) / $contracts2 * $Brodirection2 + $gross_prem2, 2);

                //set for the counter
                $netPremiumCounter1 =  round($nominal1 * ($is_sender ? $SINGLEcalendarsmallFEEReceiving : $SINGLEcalendarsmallFEESender) / $contracts1 * $counterBrodirection1 + $gross_prem1, 2);
                $netPremiumCounter2 =  round($nominal2 * ($is_sender ? $SINGLEcalendarbigFEEReceiving : $SINGLEcalendarbigFEESender) / $contracts2 * $counterBrodirection2 + $gross_prem2, 2);
            }

        } else {
            // These are percentage values
            $IXcalendarbigFEESender = $sender_org->resolveBrokerageFee($calendar_key.'index.big_leg')/100;
            $IXcalendarbigFEEReceiving = $receiving_org->resolveBrokerageFee($calendar_key.'index.big_leg')/100;

            $IXcalendarsmallFEESender = $sender_org->resolveBrokerageFee($calendar_key.'index.small_leg')/100;
            $IXcalendarsmallFEEReceiving = $receiving_org->resolveBrokerageFee($calendar_key.'index.small_leg')/100;

	        $SpotRef = $this->marketRequest->userMarketRequestTradables[0]->market->spot_price_ref;

	        if($contracts1 < $contracts2) {
	        	// NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXcalendarbigFEE * Brodirection1, 0) + GrossPrem1
		        $netPremium1 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarbigFEESender : $IXcalendarbigFEEReceiving) * $Brodirection1, 3) + $gross_prem1;
		        $netPremium2 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarsmallFEESender : $IXcalendarsmallFEEReceiving) * $Brodirection2, 3) + $gross_prem2;

		        //set for the counter
                $netPremiumCounter1 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarbigFEEReceiving : $IXcalendarbigFEESender) * $counterBrodirection1, 3) + $gross_prem1;
		        $netPremiumCounter2 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarsmallFEEReceiving : $IXcalendarsmallFEESender) * $counterBrodirection2, 3) + $gross_prem2;
            } else {
                // NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXcalendarsmallFEE * Brodirection1, 0) + GrossPrem1
                $netPremium1 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarsmallFEESender : $IXcalendarsmallFEEReceiving) * $Brodirection1, 3) + $gross_prem1;
                $netPremium2 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarbigFEESender : $IXcalendarbigFEEReceiving) * $Brodirection2, 3) + $gross_prem2;

		        //set for the counter
                $netPremiumCounter1 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarsmallFEEReceiving : $IXcalendarsmallFEESender) * $counterBrodirection1, 3) + $gross_prem1;
		        $netPremiumCounter2 =  round($SpotRef * 10 * ($is_sender ? $IXcalendarbigFEEReceiving : $IXcalendarbigFEESender) * $counterBrodirection2, 3) + $gross_prem2;
            }
        }

        // Fee = |GrossPrem - NetPremContracts| * Contracts
        $fee1 = abs($gross_prem1 - $netPremium1) * $contracts1;
        $fee2 = abs($gross_prem2 - $netPremium2) * $contracts2;
        // set for the counter
        $feeCounter1 = abs($gross_prem1 - $netPremiumCounter1) * $contracts1;
        $feeCounter2 = abs($gross_prem2 - $netPremiumCounter2) * $contracts2;

        // Phase 3 addition - Future Fee calc changes Index vs Singles
        $future_contracts1 = $this->futureGroups[0]->getOpVal('Contract');
        $future_contracts2 = $this->futureGroups[1]->getOpVal('Contract');
        $future_key = 'marketmartial.confirmation_settings.futures.'.($singleStock ? 'singles' : 'index').'.all_futures';
        //its a percentage        
        $future_fee_percentage_sender = $sender_org->resolveBrokerageFee($future_key)/100;
        $future_fee_percentage_receiving = $receiving_org->resolveBrokerageFee($future_key)/100;
        $future_fee_percentage = ($is_sender ? $future_fee_percentage_sender : $future_fee_percentage_receiving);
        $future_fee_percentage_counter = ($is_sender ? $future_fee_percentage_receiving : $future_fee_percentage_sender);
        // Future Fee = Spot * future Contracts * 100 * Fee%
        $future_fee1 = $this->calcFutureFee($SpotRef, $future_contracts1, $future_fee_percentage, $singleStock);
        $future_fee2 = $this->calcFutureFee($SpotRef, $future_contracts2, $future_fee_percentage, $singleStock);
        $future_fee_counter1 = $this->calcFutureFee($SpotRef, $future_contracts1, $future_fee_percentage_counter, $singleStock);
        $future_fee_counter2 = $this->calcFutureFee($SpotRef, $future_contracts2, $future_fee_percentage_counter, $singleStock);

        // Fee Total = SUM(Fee)
        $totalFee = round($fee1 + $fee2 + $future_fee1 + $future_fee2);
        $totalFeeCounter = round($feeCounter1 + $feeCounter2 + $future_fee_counter1 + $future_fee_counter2);

        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium1,$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremium2,$is_sender);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFee,$is_sender);

        //set for the counter
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremiumCounter1,!$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremiumCounter2,!$is_sender);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFeeCounter,!$is_sender);
    }
}