import BaseModel from './BaseModel';
import Errors from './Errors';
import UserMarketNegotiation from './UserMarketNegotiation';
import UserMarket from './UserMarket';

export default class TradeNegotiation extends BaseModel {

    constructor(options) {

      super({
            _used_model_list: [UserMarketNegotiation,UserMarket],
        });

     	// default internal
        this._user_market_request = null;
		// default internal
        this._user_market = null;
		
		const defaults = {
		    id: "",
		    quantity: "",
            trade_negotiation_id: "",
		    traded: false,
		    is_offer: null,
		    is_distpute: false,
            sent_by_me: false,
            sent_to_me: false,
		    created_at: moment(),
            updated_at: moment()
		}

        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
                if(defaults[key] instanceof moment) {
                    this[key] = moment(this[key]);
                }
            } else {
                this[key] = defaults[key];
            }
        });


    }


     /**
    *   setUserMarket - Sets the negotiations UserMarket
    *   @param {UserMarket} market - UserMarket for the negotiation
    */
    setUserMarket(user_market) {
        this._user_market = user_market;
    }

    /**
    *   getUserMarket - Gets the negotiations UserMarket
    *   @return {UserMarket}
    */
    getUserMarket() {
        return this._user_market;
    }


     /**
    *   setUserMarketNegotiation - Sets the negotiations UserMarket
    *   @param {UserMarket} market - UserMarket for the negotiation
    */
    setUserMarketNegotiation(user_market_negotiation) {
        this.user_market_negotiation = user_market_negotiation;
    }

    /**
    *   getUserMarket - Gets the negotiations UserMarket
    *   @return {UserMarket}
    */
    getUserMarketNegotiation() {
        return this.user_market_negotiation;
    }



    prepareStore() {
        return {
			id: this.id,
			quantity: this.quantity,
			traded: !!this.traded,
			is_offer: !!this.is_offer,
			is_distpute: this.is_distpute
        };
    }

    getTradingText()
    {
        let text;
        
        

        if(this.sent_by_me)
        {
            text =  this.is_offer ? "You bought @ " +this.getUserMarketNegotiation().offer : "You sold @ " +  this.getUserMarketNegotiation().bid ;

        }else if(this.sent_to_me)
        {
            text = this.is_offer ? "You sold @ "+this.getUserMarketNegotiation().offer :"You bought @ "+this.getUserMarketNegotiation().bid;
        }else
        {
            if(this.traded)
            {
                text =   this.getUserMarketNegotiation().getFirstTradeNegotiation().is_offer ? "Bought @ " +this.getUserMarketNegotiation().offer : "Sold @ " +  this.getUserMarketNegotiation().bid;
                return text + " (" + this.quantity + ") ";
            }else
            {
                text = "Trading at "; 
                text +=  this.is_offer ? this.getUserMarketNegotiation().offer : this.getUserMarketNegotiation().bid; 
            }
        }
        return text;
    }

    getSizeText()
    {
        if(this.sent_by_me)
        {
            return  "Your desired size:";
        }else if(this.sent_to_me)
        {
            return "Counterparty's requested size:";
        }
    }

    getVolLevel()
    {
        return this.is_offer ? this.getUserMarketNegotiation().offer : this.getUserMarketNegotiation().bid;
    }

    getNegotiationQuantity()
    {
        return this.is_offer ? this.getUserMarketNegotiation().offer_qty : this.getUserMarketNegotiation().bid_qty;
    }

    getQuantityOver()
    {
        if(this.trade_negotiation_id)
        {
            let relatedTradeNehotiation = this.getUserMarketNegotiation().trade_negotiations.find((trade_negotiation)=>{
               return trade_negotiation.id == this.trade_negotiation_id;
            });
            return  relatedTradeNehotiation.quantity - this.quantity;
        }else
        {
            return null;
        }
    }

    getTextOver()
    {   

        return this.is_offer ? "You have been offerd over " : "You have been bid over "; 
    }

    /**
    *  store
    */
    store(user_market_negotiation) {


        if(typeof user_market_negotiation === 'undefined') {
            // catch not assigned to a market request yet!
            if(this._user_market.id == null) {
                return new Promise((resolve, reject) => {
                    reject(new Errors(["Invalid Market"]));
                });
            }
        }
        

        return new Promise((resolve, reject) => {

             axios.post(axios.defaults.baseUrl +"/trade/market-negotiation/"+user_market_negotiation.id+"/trade-negotiation", this.prepareStore())
            .then(response => {
                //response.data.data = new UserMarketNegotiation(response.data.data);
                // link now that we are saved
                // user_market.setMarketRequest(user_market_request);
                // user_market.setCurrentNegotiation(this);
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }



    counter(tradeNegotiation)
    {
        return new Promise((resolve, reject) => {
             tradeNegotiation.is_offer = this.is_offer;
             axios.post(axios.defaults.baseUrl +"/trade/market-negotiation/"+this.user_market_negotiation.id+"/trade-negotiation", tradeNegotiation.prepareStore())
            .then(response => {
                //response.data.data = new UserMarketNegotiation(response.data.data);
                // link now that we are saved
                // user_market.setMarketRequest(user_market_request);
                // user_market.setCurrentNegotiation(this);
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }
 
}