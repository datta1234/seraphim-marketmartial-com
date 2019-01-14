<?php

namespace App\Traits;
use Carbon\Carbon;

trait CalcuatesForPhases {
	
	public function phaseTwo()
	{
        // @TODO (NANI!) what do we do with var swap?
        switch ($this->tradeStructure->title) {
           case 'Outright':
                $this->outrightTwo();
           break;
           case 'Risky':
                $this->riskyTwo();
           break;
           case 'Calendar':
                $this->calendarTwo();
           break;
           case 'Fly':
                $this->flyTwo();
           break;
           case 'Option Switch':
                $this->optionSwitchTwo();
           break;
           case 'EFP':
                $this->efpTwo();
           break;
           case 'Rolls':
                $this->rollsTwo();
           break;
           case 'EFP Switch':
                $this->efpSwitchTwo();
           break;
       }
	}

    // Removed - seems redundant and over complicates as each structures fees require different variables
    /*public function feesCalc($isOffer,$gross_prem,$is_sender,$tradables)
    {
        switch ($this->tradeStructure->title) {
           case 'Outright':
                $this->outrightFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
           case 'Risky':
                $this->riskyFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
           case 'Calendar':
                $this->calendarFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
           case 'Fly':
                $this->flyFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
           case 'Option Switch':
                $this->optionSwitchFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
           case 'EFP':
                $this->efpFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
           case 'Rolls':
                $this->rollsFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
           case 'EFP Switch':
                $this->efpSwitchFees($isOffer,$gross_prem,$is_sender,$tradables);
           break;
       }  
    }*/

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

        if(!$singleStock){
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

         if(!$singleStock){
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