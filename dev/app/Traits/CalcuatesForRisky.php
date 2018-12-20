<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForRisky {
    
    public function riskyTwo()
    {
        $this->load(['futureGroups','optionGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;
        $future1 =  floatval($this->futureGroups[0]->getOpVal('Future'));

        $contracts1 =  floatval($this->optionGroups[0]->getOpVal('Contract'));
        $expiry1 = Carbon::createFromFormat("Y-m-d",$this->optionGroups[0]->getOpVal('Expiration Date'));
        $strike1 =  floatval($this->optionGroups[0]->getOpVal('strike'));
        $volatility1 = ( floatval($this->optionGroups[0]->getOpVal('volatility'))/100);//its a percentage

        $contracts2 =  floatval($this->optionGroups[1]->getOpVal('Contract'));
        $strike2 =  floatval($this->optionGroups[1]->getOpVal('strike'));
        $volatility2 = ( floatval($this->optionGroups[1]->getOpVal('volatility'))/100);//its a percentage
 

        $is_offer = $this->optionGroups[0]->getOpVal('is_offer',true);

        // @TODO - add logic for single stock
        $contracts  = null;
        $singleStock = false;

        if($is_offer == 1) {
            $putDirection1	= 1;
            $callDirection1 = 1;
            
            $putDirection2 	= 1;
            $callDirection2 = 1; 
        } else {
            $putDirection1   = -1;
            $callDirection1  = -1;
            
            $putDirection2   = -1;
            $callDirection2  = -1;  
        }

        $startDate = Carbon::now()->startOfDay();
        
        $POD1 = $this->putOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1  * $putDirection1;
        $COD1 = $this->callOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1 * $callDirection1;

        $POD2 = $this->putOptionDelta($startDate,$expiry1,$future1,$strike2,$volatility2) * $contracts2  * $putDirection2;
        $COD2 = $this->callOptionDelta($startDate,$expiry1,$future1,$strike2,$volatility2) * $contracts2 * $callDirection2;
        



        if(abs($POD1 + $POD2) <= abs($COD1 + $COD2)) {
            //set the cell to a put
            $this->optionGroups[0]->setOpVal('is_put',true);
            $this->optionGroups[1]->setOpVal('is_put',true);
            $gross_prem1 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
            $gross_prem2 = $this->putOptionPremium($startDate,$expiry1,$future1,$strike2,$volatility2,$singleStock);
            $this->optionGroups[0]->setOpVal('Gross Premiums',$gross_prem1,$is_sender);
            $this->optionGroups[1]->setOpVal('Gross Premiums',$gross_prem2,$is_sender);

            $contracts = $POD1 + $POD2;
        } else {
           $this->optionGroups[0]->setOpVal('is_put',false);
           $this->optionGroups[1]->setOpVal('is_put',false);
           $gross_prem1 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
           $gross_prem2 = $this->callOptionPremium($startDate,$expiry1,$future1,$strike2,$volatility2,$singleStock);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem1,$is_sender);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem2,$is_sender);

          $contracts/*cell(21,6)*/ = $COD1 + $COD2;
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

        $this->futureGroups[0]->setOpVal('Contract', round($contracts));

        $this->load(['futureGroups','optionGroups']);

        $this->feesCalc($isOffer, $gross_prem1, $gross_prem2, $is_sender, $contracts1, $contracts2);
    }

    public function riskyFees($isOffer,$gross_prem1,$gross_prem2,$is_sender,$contracts1,$contracts2)
    {     
    	//get the spot price ref.
        $IXriskybigFEE = config('marketmartial.confirmation_settings.outright.index.big_leg');
        $IXriskysmallFEE = config('marketmartial.confirmation_settings.outright.index.small_leg');

        $SpotReferencePrice1 = $this->market->spot_price_ref;
        $Brodirection1 = 1;
        $Brodirection2 = 1;
       
        if(!$isOffer)
        {
            $Brodirection1 = -1;
            $Brodirection2 = -1;
        }

        if($contracts1 < $contracts2) {
	        $netPremium1 =  round($SpotReferencePrice1 * 10 * $IXriskybigFEE * $Brodirection1, 0) + $gross_prem1;
	        $netPremium2 =  round($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $Brodirection2, 0) + $gross_prem2;
        } else {
	        $netPremium1 =  round($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $Brodirection1, 0) + $gross_prem1;
	        $netPremium2 =  round($SpotReferencePrice1 * 10 * $IXriskybigFEE * $Brodirection1, 0) + $gross_prem2;
        }

        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium1,$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremium2,$is_sender);

        //set for the counter
        $counterBrodirection1 = $Brodirection1 * -1;
        $counterBrodirection2 = $Brodirection2 * -1;

        if($contracts1 < $contracts2) {
	        $netPremium1 =  round($SpotReferencePrice1 * 10 * $IXriskybigFEE * $counterBrodirection1, 0) + $gross_prem1;
	        $netPremium2 =  round($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $counterBrodirection2, 0) + $gross_prem2;
        } else {
	        $netPremium1 =  round($SpotReferencePrice1 * 10 * $IXriskysmallFEE * $counterBrodirection1, 0) + $gross_prem1;
	        $netPremium2 =  round($SpotReferencePrice1 * 10 * $IXriskybigFEE * $counterBrodirection2, 0) + $gross_prem2;
        }

        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium1,!$is_sender);
        $this->optionGroups[1]->setOpVal('Net Premiums', $netPremium2,!$is_sender)
    }
}