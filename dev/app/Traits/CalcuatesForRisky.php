<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForRisky {
    
    public function riskyTwo()
    {
        $this->load(['futureGroups','optionGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $singleStock = $this->optionGroups[0]->userMarketRequestGroup->tradable->isStock();
        
        if($singleStock) {
            $SpotRef = floatval($this->futureGroups[0]->getOpVal('Spot'));
            $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');
            $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');

            $this->optionGroups[0]->setOpVal('Contract', round( $nominal1 / ($SpotRef * 100), 0));
            $this->optionGroups[1]->setOpVal('Contract', round( $nominal2 / ($SpotRef * 100), 0));
        }

        $future1 =  floatval($this->futureGroups[0]->getOpVal('Future'));
        $contracts1 =  floatval($this->optionGroups[0]->getOpVal('Contract'));
        $expiry1 = Carbon::createFromFormat("Y-m-d",$this->optionGroups[0]->getOpVal('Expiration Date'));
        $strike1 =  floatval($this->optionGroups[0]->getOpVal('strike'));
        $volatility1 = ( floatval($this->optionGroups[0]->getOpVal('volatility'))/100);//its a percentage

        $contracts2 =  floatval($this->optionGroups[1]->getOpVal('Contract'));
        $strike2 =  floatval($this->optionGroups[1]->getOpVal('strike'));
        $volatility2 = ( floatval($this->optionGroups[1]->getOpVal('volatility'))/100);//its a percentage
 
        $future_contracts  = null;        
        
        $is_offer = $this->optionGroups[0]->getOpVal('is_offer',true);

        if($is_offer == 1) {
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

        $POD2 = round($this->putOptionDelta($startDate,$expiry1,$future1,$strike2,$volatility2) * $contracts2, 0)  * $putDirection2;
        $COD2 = round($this->callOptionDelta($startDate,$expiry1,$future1,$strike2,$volatility2) * $contracts2, 0) * $callDirection2;

        if(abs($POD1 + $POD2) <= abs($COD1 + $COD2)) {
            //set the cell to a put
            $this->optionGroups[0]->setOpVal('is_put',true);
            $this->optionGroups[1]->setOpVal('is_put',true);
            $gross_prem1 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
            $gross_prem2 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike2,$volatility2,$singleStock);
            $this->optionGroups[0]->setOpVal('Gross Premiums',$gross_prem1,$is_sender);
            $this->optionGroups[1]->setOpVal('Gross Premiums',$gross_prem2,$is_sender);

            $future_contracts = $POD1 + $POD2;
        } else {
           $this->optionGroups[0]->setOpVal('is_put',false);
           $this->optionGroups[1]->setOpVal('is_put',false);
           $gross_prem1 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
           $gross_prem2 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike2,$volatility2,$singleStock);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem1,$is_sender);
           $this->optionGroups[1]->setOpVal('Gross Premiums', $gross_prem2,$is_sender);

          $future_contracts/*cell(21,6)*/ = $COD1 + $COD2;
        }

        // futures and deltas buy/sell
        if($future_contracts < 0) {
            $isOffer = false;
        } else {
            $isOffer = true;
        }
        $this->futureGroups[0]->setOpVal('is_offer', $isOffer, true);
        $this->futureGroups[0]->setOpVal('is_offer', !$isOffer, false);

        $this->futureGroups[0]->setOpVal('Contract', abs($future_contracts));

        $this->load(['futureGroups','optionGroups']);

        $this->riskyFees($isOffer, $gross_prem1, $gross_prem2, $is_sender, $contracts1, $contracts2, $singleStock);
    }

    public function riskyFees($isOffer,$gross_prem1,$gross_prem2,$is_sender,$contracts1,$contracts2,$singleStock)
    {
    	$Brodirection1 = $isOffer ? -1 : 1;
    	$Brodirection2 = $isOffer ? 1 : -1;
        $counterBrodirection1 = $Brodirection1 * -1;
        $counterBrodirection2 = $Brodirection2 * -1;

        if($singleStock) {
	        $SINGLEriskybigFEE = config('marketmartial.confirmation_settings.risky.singles.big_leg')/100;//its a percentage
	        $SINGLEriskysmallFEE = config('marketmartial.confirmation_settings.risky.singles.small_leg')/100;//its a percentage

	        $nominal1 = $this->optionGroups[0]->getOpVal('Nominal');
	        $nominal2 = $this->optionGroups[1]->getOpVal('Nominal');

	        if($nominal1 < $nominal2) {
	        	// NETPREM = Round(nominal1 * SINGLEriskybigFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
	        	$netPremium1 =  round($nominal1 * $SINGLEriskybigFEE / $contracts1 * $Brodirection1 + $gross_prem1, 2);
		      	// NETPREM =  Round(nominal2 * SINGLEriskysmallFEE / Contracts2 * Brodirection2 + GrossPrem2, 2)
		      	$netPremium2 =  round($nominal2 * $SINGLEriskysmallFEE / $contracts2 * $Brodirection2 + $gross_prem2, 2);
		      	
	        	//set for the counter
	        	$netPremiumCounter1 =  round($nominal1 * $SINGLEriskybigFEE / $contracts1 * $counterBrodirection1 + $gross_prem1, 2);
		      	$netPremiumCounter2 =  round($nominal2 * $SINGLEriskysmallFEE / $contracts2 * $counterBrodirection2 + $gross_prem2, 2);
	        } else {
	        	// NETPREM = Round(nominal1 * SINGLEriskysmallFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
	        	$netPremium1 =  round($nominal1 * $SINGLEriskysmallFEE / $contracts1 * $Brodirection1 + $gross_prem1, 2);
		      	// NETPREM = Round(nominal2 * SINGLEriskybigFEE / Contracts2 * Brodirection2 + GrossPrem2, 2)
		      	$netPremium2 =  round($nominal2 * $SINGLEriskybigFEE / $contracts2 * $Brodirection2 + $gross_prem2, 2);

		      	//set for the counter
	        	$netPremiumCounter1 =  round($nominal1 * $SINGLEriskysmallFEE / $contracts1 * $counterBrodirection1 + $gross_prem1, 2);
		      	$netPremiumCounter2 =  round($nominal2 * $SINGLEriskybigFEE / $contracts2 * $counterBrodirection2 + $gross_prem2, 2);
	        }

        } else {
	    	//get the spot price ref.
	        $IXriskybigFEE = config('marketmartial.confirmation_settings.risky.index.big_leg')/100;//its a percentage
	        $IXriskysmallFEE = config('marketmartial.confirmation_settings.risky.index.small_leg')/100;//its a percentage

	        $SpotReferencePrice1 = $this->marketRequest->userMarketRequestTradables[0]->market->spot_price_ref;

	        if($contracts1 < $contracts2) {
		        // NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXriskybigFEE * Brodirection1, 0) + GrossPrem1
		        $netPremium1 =  floor($SpotReferencePrice1 * 10 * $IXriskybigFEE * $Brodirection1) + $gross_prem1;
		        $netPremium2 =  floor($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $Brodirection2) + $gross_prem2;
	        } else {
	        	// NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXriskysmallFEE * Brodirection1, 0) + GrossPrem1
		        $netPremium1 =  floor($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $Brodirection1) + $gross_prem1;
		        $netPremium2 =  floor($SpotReferencePrice1 * 10 * $IXriskybigFEE * $Brodirection2) + $gross_prem2;
	        }

	        //set for the counter
	        if($contracts1 < $contracts2) {
		        $netPremiumCounter1 =  floor($SpotReferencePrice1 * 10 * $IXriskybigFEE * $counterBrodirection1) + $gross_prem1;
		        $netPremiumCounter2 =  floor($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $counterBrodirection2) + $gross_prem2;
	        } else {
		        $netPremiumCounter1 =  floor($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $counterBrodirection1) + $gross_prem1;
		        $netPremiumCounter2 =  floor($SpotReferencePrice1 * 10 * $IXriskybigFEE * $counterBrodirection2) + $gross_prem2;
	        }
        }

        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium1,$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremium2,$is_sender);

        //set for the counter
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremiumCounter1,!$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremiumCounter2,!$is_sender);
    }
}