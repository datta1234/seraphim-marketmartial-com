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

            constructor(options) {
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
                    future_groups: []
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
                }

                if(options && options.structure_groups) {
                    this.setUpFutureColoumns(options.structure_groups);
                }

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

            phaseOne()
            {
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

            phaseTwo(spotRef, Zarnominal1 ,underlying1,singleStock,is_offer)
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

            }

        };
        return this.class;
    }
};
export default TradeConfirmationOutright;