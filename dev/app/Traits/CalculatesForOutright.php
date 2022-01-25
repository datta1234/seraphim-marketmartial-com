<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalculatesForOutright {
	
    public function outrightTwo()
    {
        $this->load(['futureGroups','optionGroups']);

        $organisation = $this->resolveOrganisation();
        $is_sender  = $organisation->id == $this->sendUser->organisation_id;
        
        $singleStock = $this->optionGroups[0]->userMarketRequestGroup->tradable->isStock();
        
        if($singleStock) {
            $SpotRef = floatval($this->futureGroups[0]->getOpVal('Spot'));
            // Need to multiply by 1M because the Nomninal is amount per million
            $val = round( ($this->tradeNegotiation->quantity * 1000000) / ($SpotRef * 100), 0);
            if($val === 0.0) {
                // handle cant process, spotref too high
                throw new \App\Exceptions\SpotRefTooHighException("Spot Ref Too High",0);
            }
            $this->optionGroups[0]->setOpVal('Contract', $val);
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

        /*
            Phase 3 update as requested by client
            Only use prefered Option Delta 
                i.e. abs($POD1) <= abs($COD1)
            On the initial calc
                i.e. Gross Premium not set
            After this point the put / call will dictate the set
                i.e. is_put true / false
        */
        $gross_prem_exists = !is_null($this->optionGroups[0]->getOpVal('Gross Premiums'));
        $pref_option_premium = $gross_prem_exists ? $this->optionGroups[0]->getOpVal('is_put') : (abs($POD1) <= abs($COD1));
        
        \Log::info([
            "Gross Prem Exists" =>  $gross_prem_exists,
            "Pref Option Prem" => $pref_option_premium
        ]);

        if($pref_option_premium) {
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

        $this->load(['futureGroups','optionGroups','feeGroups']);

        $this->outrightFees($is_offer,$gross_prem,$is_sender, $contracts1, $singleStock);
    }

    public function outrightFees($isOffer,$gross_prem,$is_sender,$contracts1,$singleStock)
    {     
        $Brodirection1 = $isOffer ? 1 : -1;
        $counterBrodirection1 = $Brodirection1 * -1;

        $sender_org = $this->sendUser->organisation;
        $receiving_org = $this->recievingUser->organisation;
        $outright_key = 'marketmartial.confirmation_settings.outright.';
            
        if($singleStock) {
            //its a percentage        
            $SINGLEoutrightFEESender = $sender_org->resolveBrokerageFee($outright_key.'singles.only_leg')/100;
            $SINGLEoutrightFEEReceiving = $receiving_org->resolveBrokerageFee($outright_key.'singles.only_leg')/100;

            $nominal1 = $this->tradeNegotiation->quantity * 1000000;

            //NETPREM = Round(nominal1 * SINGLEoutrightFEE / Contracts1 * Brodirection1 + GrossPrem1, 2)
            $netPremium =  round($nominal1 * ($is_sender ? $SINGLEoutrightFEESender : $SINGLEoutrightFEEReceiving) / $contracts1 * $Brodirection1 + $gross_prem, 2);

            //set for the counter
            $netPremiumCounter =  round($nominal1 * ($is_sender ? $SINGLEoutrightFEEReceiving : $SINGLEoutrightFEESender) / $contracts1 * $counterBrodirection1 + $gross_prem, 2); 
        } else {
            //its a percentage        
            $IXoutrightFEESender = $sender_org->resolveBrokerageFee($outright_key.'index.only_leg')/100;
            $IXoutrightFEEReceiving = $receiving_org->resolveBrokerageFee($outright_key.'index.only_leg')/100;    
            
            $SpotReferencePrice1 = $this->optionGroups[0]->userMarketRequestGroup->tradable->market->spot_price_ref;

            //NETPREM = Application.RoundDown(SpotReferencePrice1 * 10 * IXoutrightFEE * Brodirection1, 0) + GrossPrem1
            $netPremium =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXoutrightFEESender : $IXoutrightFEEReceiving) * $Brodirection1) + $gross_prem;

            //set for the counter
            $netPremiumCounter =  floor($SpotReferencePrice1 * 10 * ($is_sender ? $IXoutrightFEEReceiving : $IXoutrightFEESender) * $counterBrodirection1) + $gross_prem; 
        }

        // Fee = |GrossPrem - NetPremContracts| * Contracts
        $totalFee = round(abs($gross_prem - $netPremium) * $contracts1);
        // Calculate for the counter
        $totalFeeCounter = round(abs($gross_prem - $netPremiumCounter) * $contracts1);

        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium,$is_sender);

        $this->feeGroups[0]->setOpVal('Fee Total', $totalFee,$is_sender);
         
        //set for the counter
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremiumCounter,!$is_sender);
        
        $this->feeGroups[0]->setOpVal('Fee Total', $totalFeeCounter,!$is_sender);


    }
}