import BaseModel from './BaseModel';
import UserMarketRequest from './UserMarketRequest'
export default class TradeConfirmation extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: [UserMarketRequest]
        });

        const defaults = {
            id:"",
            organisation:"",
            spot_price:"",
            future_reference:"",
            near_expiery_reference:"",
            puts:"",
            calls:"",
            delta:"",
            gross_premiums:"",
            net_premiums:"",
            is_confirmed:"",
            trade_structure_title:"",
            expiration:[],
            strike:[],
            quantity:[],
            market_request_id:"",
            label:"",
            volatility:"",
            is_single_stock:"",
            underlying_id:"",
            underlying_title:"",
            is_sale:"",
            traded_at: moment()
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });
    }

    readableExpiration()
    {
        return this.expiration.length > 1 ? this.expiration.join('/') : this.expiration[0] ;
    }

    phaseOneRun(trade_confirmation)
    {
        switch(trade_confirmation.trade_structure_title) {
            case "Outright":
                    this.runOutright(trade_confirmation);
                break;
            case "Risky":
                //@TODO runRisky()
                break;
            case "Calander":
                //@TODO runCalander()
                break;
           case "Fly":
                //@TODO runRisky()
                break;
            case "Option Switch":
                //@TODO runOptionSwitch()
                break;
           case "EFP":
                //@TODO runEFP()
                break;
            case "Rolls":
                //@TODO runRoll()
                break;
            case "EFP Switch":
                //@TODO runEFPSwicth()
                break;

        }
    }

    phaseTwoRun()
    {
        switch(this.trade_structure_title) {
            case "Outright":
                    this.outrightTwo();
                break;
            case "Risky":
                //@TODO runRisky()
                break;
            case "Calander":
                //@TODO runCalander()
                break;
           case "Fly":
                //@TODO runRisky()
                break;
            case "Option Switch":
                //@TODO runOptionSwitch()
                break;
           case "EFP":
                //@TODO runEFP()
                break;
            case "Rolls":
                //@TODO runRoll()
                break;
            case "EFP Switch":
                //@TODO runEFPSwicth()
                break;

        }
    }

    outrightTwo(spotRef, Zarnominal1 ,underlying1,singleStock,is_offer)
    {
        let contracts = (Zarnominal1/ spotRef * 100).toFixed(0);

        //determine weather put or call
    

        //can reduce this logic
        if(is_offer)
        {
            let putDirection1   = 1;
            let callDirection1  = 1;   
        }else
        {
            let putDirection1  = -1;
            let callDirection1 = -1; 
        }

        let POD1 = putOptionDelta(startDate,expiry,future,volatility) * putDirection1;
        let COD1 = callOptionDelta(startDate,expiry,future,volatility) * callDirection1;

        if(Math.abs(POD1) <= Math.abs(COD1))
        {
            //set the cell to a put
            let is_put = true;
            let gross_premiums /* cell(16,10) */= (putOptionPremium(startDate,expiry1,futuref1,strike1,volatility) * contracts).toFixed(0) * putDirection1;
            let contracts/*cell(21,6)*/ = POD1;
        }else
        {
            let is_put = false;
            let gross_premiums /* cell(16,10) */= (callOptionPremium(startDate,expiry1,futuref1,strike1,volatility) * contracts).toFixed(0) * putDirection1;
            let contracts/*cell(21,6)*/ = COD1;
        }

        // futures and deltas buy/sell
        if(contracts < 0)
        {
            is_offer = false;
        }else
        {
            is_offer = true;
        }

        contracts = Math.abs(contracts);

    }


    feesCalc()
    {
        let singlefee = config('fees'.trade_structure.singles);
        let indexfee = config('fees'.trade_structure.index);
        
        switch(this.trade_structure_title) {
            case "Outright":

                Math.round(spotRefPrice * 10 * Brodirection) + gross_premiums1 ;

                break;
            case "Risky":
                //@TODO runRisky()
                break;
            case "Calander":
                //@TODO runCalander()
                break;
           case "Fly":
                //@TODO runRisky()
                break;
            case "Option Switch":
                //@TODO runOptionSwitch()
                break;
           case "EFP":
                //@TODO runEFP()
                break;
            case "Rolls":
                //@TODO runRoll()
                break;
            case "EFP Switch":
                //@TODO runEFPSwicth()
                break;
        }

    }


    runOutright(trade_confirmation)
    {
        console.log(trade_confirmation);
        console.log("run outright");

        //apply logic for single stock
        let buyer =  true;//@TODO setup if trader is a buyer or seller;
        
        this.trade_structure_title = trade_confirmation.trade_structure_title;
        this.underlying_title = trade_confirmation.underlying_title;
        this.underlying_id = trade_confirmation.underlying_id;

         //Strike 
         //strike 1
        this.stike = trade_confirmation.strike;


        /* nominals/contracts */
        this.quantity = trade_confirmation.quantity;
        
        /*Expiry*/
        this.expiration[0] = trade_confirmation.expiration[0]; 

        /*volatilty*/
        this.volatility = trade_confirmation.volatility;

        /* single stock (true) or index false*/
        this.is_single_stock = trade_confirmation.is_single_stock;
    }

    runRisky()
    {

    }

    /*
    * formulas from the macros
    */
    callOptiondelta(startDate,expiry,future,volatility)
    {
        let tt = (expiry - startDate) / 365;

        let callOptionDelta = ((future/strike) + 0.5 * volatility ^2 * tt) / (volatility * tt ^0.5);

        return - this.normSdist(callOptionDelta);
    }

    putOptionDelta(startDate,expiry,future,volatility)
    {
        let tt = (expiry - startDate) / 365;
        let d1 = (this.Ln(startDate/strike) + 0.5 * volatility ^ 2 * tt) / ( volatility * tt ^ 0.5);
        return this.normSdist(-d1);
    }

    callOptionPremium(startDate,expiry,future,volatility,singleStock)
    {
        let tt = (expiry - startDate) / 365;
        let h = this.Ln((future/strike)) / (volatility * tt ^ 0.5) + (volatility * tt^0.5) /2;
        let callOptionPremium = future * this.normSdist(h) - strike * this.normSdist(h - volatility * tt ^ 0.5);

        if(singleStock){
            return (callOptionPremium*10).toFixed(0);
        }else{
            return (callOptionPremium*100).toFixed(2);
        }  
    }

    putOptionPremium(startDate,expiry,future,strike,volatility,singleStock)
    {
        let tt = (expiry - startDate) / 365;
        let h = (this.Ln(future/strike)) / (volatility * tt ^ 0.5 ) / (volatility * tt ^ 0.5 ) / 2;

        let callOptionPremium = future * this.normSdist(h) - strike * this.normSdist(h - volatility * tt ^ 0.5);

         if(singleStock){
            return (callOptionPremium*10).toFixed(0);
        }else{
            return (callOptionPremium*100).toFixed(2);
        }   
    }


    /*
    *https://docs.microsoft.com/en-us/dotnet/api/microsoft.office.interop.excel.worksheetfunction.normsdist?view=excel-pia
    *Returns the standard normal cumulative distribution function. The distribution has a mean of 0 (zero) and a standard deviation of one. Use this function in place of a table of standard normal curve areas.
    */
    normSdist(value)
    {
        return .5*(1+math.erf(value/Math.sqrt(2)));
    }

    /*
    *https://docs.microsoft.com/en-us/office/vba/api/excel.worksheetfunction.ln
    *Returns the natural logarithm of a number. Natural logarithms are based on the constant e (2.71828182845904) 
    */
    Ln(value)
    {
        return Math.log(value);
    }

}