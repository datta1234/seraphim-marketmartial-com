<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForOutright {
	
    public function outrightTwo()
    {
        $this->load(['futureGroups','optionGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;
        
        $singleStock = $this->optionGroups[0]->userMarketRequestGroup->tradable->isStock();
        
        if($singleStock) {
            $SpotRef = floatval($this->futureGroups[0]->getOpVal('Spot'));
            // Need to multiply by 1M because the Nomninal is amount per million
            $this->optionGroups[0]->setOpVal('Contract', round( ($this->tradeNegotiation->quantity * 1000000) / ($SpotRef * 100), 0));
        }
        
        $future1 =  floatval($this->futureGroups[0]->getOpVal('Future'));
        $contracts1 =  floatval($this->optionGroups[0]->getOpVal('Contract'));
        $expiry1 = Carbon::createFromFormat("Y-m-d",$this->optionGroups[0]->getOpVal('Expiration Date'));
        $strike1 =  floatval($this->optionGroups[0]->getOpVal('strike'));
        $volatility1 = ( floatval($this->optionGroups[0]->getOpVal('volatility'))/100);//its a percentage
        
        $future_contracts  = null;       
        
        $is_offer = $this->optionGroups[0]->getOpVal('is_offer',true);
        
        if($is_offer == 1) {
            $putDirection1  = 1;
            $callDirection1 = 1; 
        } else {
            $putDirection1   = -1;
            $callDirection1  = -1;  
        }

        $startDate = Carbon::now()->startOfDay();

        $POD1 = round($this->putOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1, 0) * $putDirection1;
        $COD1 = round($this->callOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1, 0) * $callDirection1;

        if(abs($POD1) <= abs($COD1)) {
            //set the cell to a put
            $this->optionGroups[0]->setOpVal('is_put',true);
            $gross_prem = $this->putOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
            $this->optionGroups[0]->setOpVal('Gross Premiums',$gross_prem,$is_sender);

            $future_contracts = $POD1;
        } else {
           $this->optionGroups[0]->setOpVal('is_put',false);
           $gross_prem = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem,$is_sender);
          $future_contracts/*cell(21,6)*/ = $COD1;
        }

        // futures and deltas buy/sell
        $isFutureOffer = !($future_contracts < 0);
        $this->futureGroups[0]->setOpVal('is_offer', $isFutureOffer, true);
        $this->futureGroups[0]->setOpVal('is_offer', !$isFutureOffer, false);

        $this->futureGroups[0]->setOpVal('Contract', abs($future_contracts));

        $this->load(['futureGroups','optionGroups']);

        $this->outrightFees($is_offer,$gross_prem,$is_sender, $contracts1, $singleStock);
    }

    public function outrightFees($isOffer,$gross_prem,$is_sender,$contracts1,$singleStock)
    {     
        $Brodirection1 = $isOffer ? 1 : -1;
        $counterBrodirection1 = $Brodirection1 * -1;
            
        if($singleStock) {
            $SINGLEoutrightFEE = config('marketmartial.confirmation_settings.outright.singles.only_leg')/100;//its a percentage

            $nominal1 = $this->tradeNegotiation->quantity * 1000000;

            //NETPREM = Round(nominal1 * SINGLEoutrightFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
            $netPremium =  round($nominal1 * $SINGLEoutrightFEE / $contracts1 * $Brodirection1 + $gross_prem, 2);

            //set for the counter
            $netPremiumCounter =  round($nominal1 * $SINGLEoutrightFEE / $contracts1 * $counterBrodirection1 + $gross_prem, 2); 
        } else {
            //get the spot price ref.
            $IXoutrightFEE = config('marketmartial.confirmation_settings.outright.index.only_leg')/100;//its a percentage       
            
            $SpotReferencePrice1 = $this->optionGroups[0]->userMarketRequestGroup->tradable->market->spot_price_ref;

            //NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXoutrightFEE * Brodirection1, 0) + GrossPrem1
            $netPremium =  floor($SpotReferencePrice1 * 10 * $IXoutrightFEE * $Brodirection1) + $gross_prem;

            //set for the counter
            $netPremiumCounter =  floor($SpotReferencePrice1 * 10 * $IXoutrightFEE * $counterBrodirection1) + $gross_prem; 
        }

        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium,$is_sender);
        
        //set for the counter
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremiumCounter,!$is_sender);


    }
}