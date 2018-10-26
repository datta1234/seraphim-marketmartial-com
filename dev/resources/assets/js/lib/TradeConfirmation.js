import BaseModel from './BaseModel';
import TCStructures from './tradeconfirmations/index';

export default class TradeConfirmation extends BaseModel {

    constructor(options,fees) {
      
        super();

        const defaults = {
            id : "",
            organisation : "",
            trade_structure_title : "",
            volatility : "",
            structure_groups : "",
            option_groups:[],
            market_request_id : "",
            market_request_title : "",
            underlying_id : "",
            underlying_title : "",
            is_single_stock : "",
            traded_at : "",
            is_offer : "",
            brokerage_fee: [],
            date: ""
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });
        this.brokerage_fee = fees;
    }

    static parse(json,fees)
    {
        console.log(fees);
        switch(json.trade_structure_title) {
            case "Outright":
                   return new  TCStructures.TradeConfirmationOutright(json,fees.options_brokarage_fees.Outright);
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

 
    /*
    * formulas from the macros
    */
    callOptionDelta(startDate,expiry,future,strike,volatility)
    {
        let tt = expiry.diff(startDate, 'days',true) / 365;
        let callOptionDelta = (this.Ln(future/strike) + 0.5 * Math.pow(volatility,2) * tt) / ( volatility * Math.pow(tt,0.5));
        let COD = -this.normSdist(callOptionDelta);
        return COD;
    }

    putOptionDelta(startDate,expiry,future,strike,volatility)
    {
        let tt = expiry.diff(startDate, 'days',true) / 365;
        let d1 = (this.Ln(future/strike) + 0.5 * Math.pow(volatility,2) * tt) / ( volatility * Math.pow(tt,0.5));       
        let POD = this.normSdist(-d1);
        return POD;
    }

    callOptionPremium(startDate,expiry,future,strike,volatility,singleStock)
    {
        let tt = expiry.diff(startDate, 'days',true) / 365;

        let h = this.Ln(future/strike) / (volatility * Math.pow(tt,0.5) ) + (volatility * Math.pow(tt,0.5) ) / 2;
        
        console.log("tt",tt);
        console.log("future",future);
        console.log("strike",strike);
        console.log("tt",volatility);
        console.log("callOptionPremium h",h);

        let callOptionPremium = future * this.normSdist(h) - strike * this.normSdist(h - volatility * Math.pow(tt,0.5));

        if(singleStock){
            return (callOptionPremium*10).toFixed(0);
        }else{
            return (callOptionPremium*100).toFixed(2);
        }  
    }

    putOptionPremium(startDate,expiry,future,strike,volatility,singleStock)
    {
        let tt = expiry.diff(startDate, 'days',true) / 365;
        let h = this.Ln(future/strike) / (volatility * Math.pow(tt,0.5) ) + (volatility * Math.pow(tt,0.5) ) / 2;
        
        let putOptionPremium = -future * this.normSdist(-h) + strike * this.normSdist(volatility * Math.pow(tt,0.5) - h);
         
         if(singleStock){
                return (putOptionPremium*100).toFixed(2);

        }else{
                return (putOptionPremium*10).toFixed(0);
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
TCStructures.init();
