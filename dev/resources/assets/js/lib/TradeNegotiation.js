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
			traded: this.traded,
			is_offer: !!this.is_offer,
			is_distpute: this.is_distpute
        };
    }

    getTradingText()
    {
        let text;
        if(this.sent_by_me)
        {
            text =  this.is_offer ? "You bought @" : "You Sold @ ";
            text += this.getUserMarketNegotiation().offer;

        }else if(this.sent_to_me)
        {
            text = this.is_offer ? "You sold @ " : "You bought @ ";
            text += this.getUserMarketNegotiation().bid;
        }else
        {
            text = '';
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
 
}