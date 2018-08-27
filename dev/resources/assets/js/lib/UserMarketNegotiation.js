import BaseModel from './BaseModel';
import Errors from './Errors';

export default class UserMarketNegotiation extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: []
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
            cond_is_repeat_atw: null,
            cond_fok_apply_bid: null,
            cond_fok_spin: null,
            cond_timeout: null,
            cond_is_ocd: null,
            cond_is_subject: null,
            cond_buy_mid: null,
            cond_buy_best: null,
            is_interest: false,
            is_maker:false,
            is_my_org:false,
            market_negotiation_id: null,
            time:null,
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
            is_repeat: !!this.is_repeat,
            has_premium_calc: !!this.has_premium_calc,
            bid_premium: this.bid_premium,
            offer_premium: this.offer_premium,
            conditions: this.conditions.map(x => x.prepareStore()),
        };
    }

    /**
    *  store
    */
    storeNegotiation() {
        // catch not assigned to a market request yet!
        if(this._user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market"]));
            });
        }
     

        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+this._user_market.id+"/market-negotiation", this.prepareStore())
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }

    getDisplayCondition()
    {
       
    }

    /**
    *  spin
    */
    spinNegotiation() {

        // catch not assigned to a market request yet!
        if(this._user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market"]));
            });
        }
        
        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+this._user_market.id+"/market-negotiation",{is_repeat: true})
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
    *  store
    */
    patchQuote() {

        // catch not assigned to a market request yet!
        if(this._user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market"]));
            });
        }


        return new Promise((resolve, reject) => {
            let user_market_request_id = this.getUserMarket().getMarketRequest().id;

             axios.patch(axios.defaults.baseUrl +"/trade/user-market-request/" + user_market_request_id+"/user-market/"+this._user_market.id, this.prepareStore())
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
    *  store
    */
    repeatQuote() {

        // catch not assigned to a market request yet!
        if(this._user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market"]));
            });
        }


        return new Promise((resolve, reject) => {
            let user_market_request_id = this.getUserMarket().getMarketRequest().id;

             axios.patch(axios.defaults.baseUrl +"/trade/user-market-request/" + user_market_request_id+"/user-market/"+this._user_market.id,{'is_repeat':true})
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }


}