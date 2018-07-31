import BaseModel from './BaseModel';
import Errors from './Errors';

export default class UserMarketNegotiation extends BaseModel {

    constructor(options) {
        super({
            used_model_list: []
        });

        // default internal
        this._user_market = null;
        // default public
        this.conditions = [];
        const defaults = {
            id: "",
            bid: "",
            offer: "",
            bid_qty: 500,
            offer_qty: 500,
            is_repeat: false,
            has_premium_calc: false,
            bid_premium: "",
            offer_premium: "",
            is_put: false,
            status: "",
            created_at: moment(),
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

        // register conditions
        if(options && options.user_market_negotiation_condition) {
            this.addUserMarketNegotiationConditions(options.user_market_negotiation_condition);
        }
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
    *   addNegotiation - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    addUserMarketNegotiationCondition(user_market_negotiation_condition) {
        user_market_negotiation_condition.setUserMarketNegotiation(this);
        this.conditions.push(user_market_negotiation_condition);
    }

    /**
    *   addNegotiations - add array of user market_negotiations
    *   @param {Array} market_negotiations - array of UserMarketNegotiation objects
    */
    addUserMarketNegotiationConditions(user_market_negotiation_conditions) {
        user_market_negotiation_conditions.forEach(user_market_negotiation_condition => {
            this.addUserMarketNegotiationCondition(user_market_negotiation_condition);
        });
    }
  
    prepareStore() {
        return {
            id: this.id,
            bid: this.bid,
            offer: this.offer,
            bid_qty: this.bid_qty,
            offer_qty: this.offer_qty,
            is_repeat: this.is_repeat,
            has_premium_calc: this.has_premium_calc,
            bid_premium: this.bid_premium,
            offer_premium: this.offer_premium,
            conditions: this.conditions.map(x => x.prepareStore()),
        };
    }

    /**
    *  store
    */
    patch() {

        console.log("here it goes off",this._user_market.id)

        // catch not assigned to a market request yet!
        if(this._user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market"]));
            });
        }

        return new Promise((resolve, reject) => {
             axios.patch(axios.defaults.baseUrl + "/trade/user-market/"+this._user_market.id+"/market-negotiation/"+this.id, this.prepareStore())
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }

    /**
    *   update - updates this User Market Quote
    *   @param {UserMarketQuote} user_market_quote - UserMarketQuote object
    */
    update(user_market_quote) {
        if(user_market_quote !== null){
            Object.entries(user_market_quote).forEach( ([key, value]) => {
                if(value !== null){
                    if(key == "_user_market") {
                        this.setUserMarket(value);
                    } else {
                        this[key] = value;
                    }
                }
            });
        }
    }
}