<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForOutright {
	
    public function outrightTwo()
    {
        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;

        $future1 =  floatval($this->futureGroups[0]->getOpVal('Future'));

        $contracts1 =  floatval($this->optionGroups[0]->getOpVal('Contract'));
        $expiry1 = Carbon::createFromFormat("Y-m-d",$this->optionGroups[0]->getOpVal('Expiration Date'));

        $strike1 =  floatval($this->optionGroups[0]->getOpVal('strike'));
        $volatility1 = ( floatval($this->optionGroups[0]->getOpVal('volatility'))/100);//its a percentage
        
        $putDirection1   = 1;
        $callDirection1  = 1; 
        $contracts  = null;


        $singleStock = false;
        
//         dd(
// $future1,
// $contracts1,
// $expiry1,
// $strike1 ,
// $volatility1
//         );

        //determine weather put or call
 
        //can reduce this logic
        $is_offer = $this->futureGroups[0]->getOpVal('is_offer');
        
        if($is_offer == 1)
        {
            $putDirection1  = 1;
            $callDirection1 = 1; 
        }
        $startDate = Carbon::now()->startOfDay();


        $POD1 = $this->putOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1  * $putDirection1;
        $COD1 = $this->callOptionDelta($startDate,$expiry1,$future1,$strike1,$volatility1) * $contracts1 * $callDirection1;

        if(abs($POD1) <= abs($COD1))
        {
            //set the cell to a put
            $this->optionGroups[0]->setOpVal('is_put',true);
            $gross_prem = $this->putOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
            $this->optionGroups[0]->setOpVal('Gross Premiums',$gross_prem,$is_sender);

            $contracts = $POD1;
        }else
        {
           $this->optionGroups[0]->setOpVal('is_put',false);
           $gross_prem = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem,$is_sender);
          $contracts/*cell(21,6)*/ = $COD1;
        }

        // dd( $POD1, $COD1,$gross_prem);
        // futures and deltas buy/sell
        if($contracts < 0)
        {
            $isOffer = false;
            $this->futureGroups[0]->setOpVal('is_offer', $isOffer);
        }else
        {
            $isOffer = true;
            $this->futureGroups[0]->setOpVal('is_offer',$isOffer);
        }

        $this->futureGroups[0]->setOpVal('Contract', round($contracts));
        $this->feesCalc($isOffer,$gross_prem,$is_sender);
    }

    public function outrightFees($isOffer,$gross_prem,$is_sender)
    {     
       //get the spot price ref.
        $IXoutrightFEE = config('marketmartial.confirmation_settings.outright.index.only_leg');
        $SpotReferencePrice1 = $this->market->spot_price_ref;
        $Brodirection1 = 1;
       
        if(!$isOffer)
        {
            $Brodirection1 = -1;
        }

        //NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXoutrightFEE * Brodirection1, 0) + GrossPrem1
        $netPremium =  round($SpotReferencePrice1 * 10 * $IXoutrightFEE * $Brodirection1, 0) + $gross_prem; 
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium,$is_sender);

        //set for the counter
        $counterBrodirection1 = $Brodirection1 * -1;
        $netPremium =  round($SpotReferencePrice1 * 10 * $IXoutrightFEE * $counterBrodirection1 , 0) + $gross_prem; 
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium,!$is_sender);

    }
}