<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForOptionSwitch {
    
    public function optionSwitchTwo()
    {
        $this->load(['futureGroups','optionGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

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
        $tradables = $this->marketRequest->userMarketRequestTradables;
        $singleStock1 = $tradables[0]->isStock();
        $singleStock2 = $tradables[1]->isStock();
        
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
        
        $POD1 = $this->putOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1  * $putDirection1;
        $COD1 = $this->callOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1 * $callDirection1;

        $POD2 = $this->putOptionDelta($startDate,$expiry2,$future2,$strike2,$volatility2) * $contracts2  * $putDirection2;
        $COD2 = $this->callOptionDelta($startDate,$expiry2,$future2,$strike2,$volatility2) * $contracts2 * $callDirection2;

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
        if($contracts < 0) {
            $isOffer = false;
            $this->futureGroups[0]->setOpVal('is_offer', $isOffer);
            $this->futureGroups[1]->setOpVal('is_offer', $isOffer);
        } else {
            $isOffer = true;
            $this->futureGroups[0]->setOpVal('is_offer',$isOffer);
            $this->futureGroups[1]->setOpVal('is_offer',$isOffer);
        }

        $this->futureGroups[0]->setOpVal('Contract', round($future_contracts1));
        $this->futureGroups[1]->setOpVal('Contract', round($future_contracts2));

        $this->load(['futureGroups','optionGroups']);

        $this->optionSwitchFees($isOffer, $gross_prem1, $gross_prem2, $is_sender, $contracts1, $contracts2,$singleStock1,$singleStock2);
    }

    public function optionSwitchFees($isOffer,$gross_prem1,$gross_prem2,$is_sender,$contracts1,$contracts2,$singleStock1,$singleStock2)
    {     
		$Brodirection = $isOffer ? 1 : -1;
        $counterBrodirection = $Brodirection * -1;

		$SINGLEoptionswitchFEE = config('marketmartial.confirmation_settings.option_switch.singles.per_leg');
		$IXoptionswitchFEE = config('marketmartial.confirmation_settings.option_switch.index.per_leg');
        

    	// Leg1 Top40, DTop, DCap or Single?
    	if($singleStock1) {
        	$user_market_request_groups = $this->tradeNegotiation->userMarket->userMarketRequest->userMarketRequestGroups;
            $nominal1 = $user_market_request_groups[0]->getDynamicItem('Quantity');

    		//NETPREM = Application.Round(nominal1 * SINGLEoptionswitchFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
    		$netPremium1 =  round($nominal1 * $SINGLEoutrightFEE / $contracts1 * $Brodirection + $gross_prem1, 2);
    		//set for the counter
    		$netPremiumCounter1 = round($nominal1 * $SINGLEoutrightFEE / $contracts1 * $counterBrodirection + $gross_prem1, 2);
    	} else {
			$SpotReferencePrice1 = $this->marketRequest->userMarketRequestTradables[0]->market->spot_price_ref;

    		//NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXoptionswitchFEE * Brodirection1, 0) + GrossPrem1
    		$netPremium1 =  round($SpotReferencePrice1 * 10 * $IXoutrightFEE * $Brodirection, 0) + $gross_prem1;
    		//set for the counter
    		$netPremiumCounter1 =  round($SpotReferencePrice1 * 10 * $IXoutrightFEE * $counterBrodirection, 0) + $gross_prem1;
    	}

    	// Leg2 Top40, DTop, DCap or Single?
    	if($singleStock2) {
    		$user_market_request_groups = $this->tradeNegotiation->userMarket->userMarketRequest->userMarketRequestGroups;
            $nominal2 = $user_market_request_groups[1]->getDynamicItem('Quantity');

    		//NETPREM = Application.Round(nominal2 * SINGLEoptionswitchFEE / Contracts2 * Brodirection2 + GrossPrem2, 2)
    		$netPremium2 =  round($nominal2 * $SINGLEoutrightFEE / $contracts2 * $Brodirection + $gross_prem2, 2);
    		//set for the counter
    		$netPremiumCounter2 =  round($nominal2 * $SINGLEoutrightFEE / $contracts2 * $counterBrodirection + $gross_prem2, 2);
    	} else {
    		$SpotReferencePrice2 = $this->marketRequest->userMarketRequestTradables[1]->market->spot_price_ref;

    		//NETPREM = Application.RoundDown(SpotReferencePrice2 * 10 * IXoptionswitchFEE * Brodirection2, 0) + GrossPrem2
    		$netPremium2 =  round($SpotReferencePrice2 * 10 * $IXoutrightFEE * $Brodirection, 0) + $gross_prem2;
    		//set for the counter
    		$netPremiumCounter2 =  round($SpotReferencePrice2 * 10 * $IXoutrightFEE * $counterBrodirection, 0) + $gross_prem2;
    	}

    	$this->optionGroups[0]->setOpVal('Net Premiums', $netPremium1,$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremium2,$is_sender);

        //set for the counter
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremiumCounter1,!$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremiumCounter2,!$is_sender);
    }
}