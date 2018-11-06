<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForPhases {
	
	public function phaseTwo()
	{
       switch ($this->tradeStructure->title) {
           case 'Outright':
                $this->outrightTwo();
               break;
       }
	}

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

        //determine weather put or call
 
        //can reduce this logic
        $is_offer = $this->futureGroups[0]->getOpVal('is_offer');
        
        if($is_offer == 1)
        {
            $putDirection1  = -1;
            $callDirection1 = -1; 
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
           $this->optionGroups[0]->setOpVal('is_put',true);
           $gross_prem = $this->callOptionPremium($startDate,$expiry1,$future1,$strike1,$volatility1,$singleStock);
           $this->optionGroups[0]->setOpVal('Gross Premiums', $gross_prem,$is_sender);
          $contracts/*cell(21,6)*/ = $COD1;
        }

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

    public function counterFeesCalc($isOffer,$gross_prem,$is_sender)
    {
        $Brodirection1 = 1;

        if(!$isOffer)
        {
            $Brodirection1 = -1;
        }

        if(!$is_sender)
        {
            $Brodirection1 = $Brodirection1 * -1;
        }

        $netPremium =  round($SpotReferencePrice1 * 10 * $IXoutrightFEE * $Brodirection1, 0) + $gross_prem; 
        $this->optionGroups[0]->setOpVal('Net Premiums', $netPremium,$is_sender);
    }


    public function feesCalc($isOffer,$gross_prem)
    {
         
        //get the spot price ref.
        $IXoutrightFEE = 0.004;
        $SpotReferencePrice1 = 50000; //@todo use admin value
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

 	/*
    * formulas from the macros
    */
    public function callOptionDelta($startDate,$expiry,$future,$strike,$volatility)
    {
        $tt = $expiry->diffInDays($startDate) / 365;
        $callOptionDelta = ($this->Ln($future/$strike) + 0.5 * pow($volatility,2) * $tt) / ( $volatility * pow($tt,0.5));


        $COD = -$this->normSdist($callOptionDelta);
        return $COD;
    }

    public function putOptionDelta($startDate,$expiry,$future,$strike,$volatility)
    {
        $tt = $expiry->diffInDays($startDate) / 365;
        $d1 = ($this->Ln($future/$strike) + 0.5 * pow($volatility,2) * $tt) / ($volatility * pow($tt,0.5)); 

        $POD = $this->normSdist(-$d1);
        return $POD;
    }

    public function callOptionPremium($startDate,$expiry,$future,$strike,$volatility,$singleStock)
    {
        $tt = $expiry->diffInDays($startDate) / 365;

        $h = $this->Ln($future/$strike) / ($volatility * pow($tt,0.5) ) + ($volatility * pow($tt,0.5) ) / 2;

        $callOptionPremium = $future * $this->normSdist($h) - $strike * $this->normSdist($h - $volatility * pow($tt,0.5));

        if($singleStock){
            return round($callOptionPremium*10);
        }else{
            return round($callOptionPremium*100,2);
        }  
    }

    public function putOptionPremium($startDate,$expiry,$future,$strike,$volatility,$singleStock)
    {
        $tt = $expiry->diffInDays($startDate) / 365;
        $h = $this->Ln($future/$strike) / ($volatility * pow($tt,0.5) ) + ($volatility * pow($tt,0.5) ) / 2;
        
        $putOptionPremium = -$future * $this->normSdist(-$h) + $strike * $this->normSdist($volatility * pow($tt,0.5) - $h);
         
         if($singleStock){
            return round($putOptionPremium*10);

        }else{
            return round($putOptionPremium*100,2);
        }   
    }


    // start excel calculations

    /*
    *https://docs.microsoft.com/en-us/dotnet/api/microsoft.office.interop.excel.worksheetfunction.normsdist?view=excel-pia
    *Returns the standard normal cumulative distribution function. The distribution has a mean of 0 (zero) and a standard deviation of one. Use this function in place of a table of standard normal curve areas.
    */
    public function normSdist($value)
    {
        return .5*(1+$this->errorFunction($value/sqrt(2)));
    }

    /*
    *https://docs.microsoft.com/en-us/office/vba/api/excel.worksheetfunction.ln
    *Returns the natural logarithm of a number. Natural logarithms are based on the constant e (2.71828182845904) 
    */
    public function Ln($value)
    {
        return log($value);
    }

    /**
     * https://github.com/markrogoyski/math-php/blob/master/src/Functions/Special.php
     * Error function (Gauss error function)
     * https://en.wikipedia.org/wiki/Error_function
     *
     * This is an approximation of the error function (maximum error: 1.5×10−7)
     *
     * erf(x) ≈ 1 - (a₁t + a₂t² + a₃t³ + a₄t⁴ + a₅t⁵)ℯ^-x²
     *
     *       1
     * t = ------
     *     1 + px
     *
     * p = 0.3275911
     * a₁ = 0.254829592, a₂ = −0.284496736, a₃ = 1.421413741, a₄ = −1.453152027, a₅ = 1.061405429
     *
     * @param  float $x
     *
     * @return float
     */
    public  function errorFunction(float $x): float
    {
        if ($x == 0) {
            return 0;
        }
        $p  = 0.3275911;
        $t  = 1 / ( 1 + $p*abs($x) );
        $a₁ = 0.254829592;
        $a₂ = -0.284496736;
        $a₃ = 1.421413741;
        $a₄ = -1.453152027;
        $a₅ = 1.061405429;
        $error = 1 - ( $a₁*$t + $a₂*$t**2 + $a₃*$t**3 + $a₄*$t**4 + $a₅*$t**5 ) * exp(-abs($x)**2);
        return ( $x > 0 ) ? $error : -$error;
    }


}