import TradeConfirmation from '../TradeConfirmation';
import OptionGroup from './OptionGroup';
import FutureGroup from './FutureGroup';

const TradeConfirmationOutright = {
    class: null,
    init: () => {
        if(this.class != null) {
            return this.class;
        }
        this.class = class TradeConfirmationOutright extends TradeConfirmation {

            constructor(options,fees) {
                super();

                const defaults = {
                    id : "",
                    organisation : "",
                    trade_structure_title : "",
                    volatility : "",
                    structure_groups : [], 
                    market_request_id : "",
                    market_request_title : "",
                    underlying_id : "",
                    underlying_title : "",
                    is_single_stock : "",
                    traded_at : "",
                    is_offer : "",
                    option_groups: [],
                    future_groups: [],
                    brokerage_fee: [],
                    date: null
                }
                // assign options with defaults
                Object.keys(defaults).forEach(key => {
                    if(options && typeof options[key] !== 'undefined') {
                        this[key] = options[key];
                    } else {
                        this[key] = defaults[key];
                    }
                });

                if(options && options.structure_groups) {

                    this.setUpOptionColoumns(options.structure_groups);
                    this.setUpFutureColoumns(options.structure_groups);
                }

                this.brokerage_fee = fees;

            }

            setUpOptionColoumns(tradeStructureGroup)
            {
                tradeStructureGroup.forEach((group)=>{
                   
                    if(group.is_options)
                    {
                        let option_group = new OptionGroup();
                        option_group.constructFromStructureGroup(group);
                        this.option_groups.push(option_group);   
                    }
                   
                });
            }

            setUpFutureColoumns(tradeStructureGroup)
            {
                tradeStructureGroup.forEach((group)=>{
                   
                    if(!group.is_options)
                    {
                        let future_group = new FutureGroup();
                        future_group.constructFromStructureGroup(group);
                        this.future_groups.push(future_group);   
                    }
                   
                }); 
            }


            phaseTwo()
            {

                let startDate = moment(this.date,"YYYY-MM-DD");

                //
                let spotRef1  = this.option_groups[0].spot;
                let zarnominal1  = this.option_groups[0].nominal;
                let underlying1  = this.option_groups[0].underlying_title;

                let ssForIndex  = this.is_single_stock;


                let future1 = this.future_groups[0].future;
                let contracts1 = this.future_groups[0].contracts;

                let expiry1 = moment(this.option_groups[0].expires_at, "YYYY-MM-DD");

                let strike1 = this.option_groups[0].strike;
                let volatility1 = (this.option_groups[0].volatility/100);//its a percentage


                let putDirection1   = 1;
                let callDirection1  = 1; 

                let contracts  = null;

                //determine weather put or call
         
                //can reduce this logic
                if(!this.future_groups[0].is_offer)
                {
                    putDirection1  = -1;
                    callDirection1 = -1; 
                }

                let POD1 = Math.round(this.putOptionDelta(startDate,expiry1,future1,strike1,volatility1)) * putDirection1;
                let COD1 = Math.round(this.callOptionDelta(startDate,expiry1,future1,strike1,volatility1)) * callDirection1;

                console.log("POD1",Math.abs(POD1));
                console.log("COD1",Math.abs(COD1));

                if(Math.abs(POD1) <= Math.abs(COD1))
                {

                    //set the cell to a put
                    this.option_groups[0].is_put = true;
                    this.option_groups[0].gross_prem /* cell(16,10) */= (this.putOptionPremium(startDate,expiry1,future1,strike1,volatility1) * contracts1).toFixed(0) * putDirection1;
                    contracts/*cell(21,6)*/ = POD1;
                }else
                {
                    this.option_groups[0].is_put = false;
                    this.option_groups[0].gross_prem /* cell(16,10) */= (this.callOptionPremium(startDate,expiry1,future1,strike1,volatility1) * contracts1).toFixed(0) * putDirection1;
                    contracts/*cell(21,6)*/ = COD1;
                }

                // futures and deltas buy/sell
                if(contracts < 0)
                {
                    this.option_groups[0].is_offer  = false;
                }else
                {
                    this.option_groups[0].is_offer  = true;
                }

                this.future_groups[0].contracts = Math.abs(contracts);

                this.feesCalc();
            }

            feesCalc()
            {
                //get the spot price ref.
                let spotref = 
                this.option_groups[0].net_prem =  (this.option_groups[0].nominal * this.brokerage_fee[0].index / this.future_groups[0].quantity + this.option_groups[0].gross_prem).toFixed(2);

            }

        };
        return this.class;
    }
};
export default TradeConfirmationOutright;