import BaseModel from './BaseModel';
import Errors from './Errors';
import TradeNegotiation from './TradeNegotiation';

export default class UserMarketNegotiation extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: [TradeNegotiation],
            _relations:{
               trade_negotiations:{
                    addMethod: (trade_negotiation) => { this.addTradeNegotiation(trade_negotiation) },
               } 
            }
        });

        // default internal
        this._user_market = null;

           // default public
        this.trade_negotiations = [];

        const defaults = {
            id: "",
            bid: "",
            offer: "",
            bid_qty: 500,
            offer_qty: 500,
            bid_display: "",
            offer_display: "",
            is_repeat: false,
            has_premium_calc: false,
            bid_premium: "",
            offer_premium: "",
            is_put: false,
            status: "",
            is_private: false,
            cond_is_repeat_atw: null,
            cond_fok_apply_bid: null,
            cond_fok_spin: null,
            cond_timeout: null,
            cond_is_oco: null,
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

        // register trade_negotiaions

        if(options && options.trade_negotiations) {
            this.addTradeNegotiations(options.trade_negotiations);
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
            is_private: this.is_private,
            cond_is_repeat_atw: this.cond_is_repeat_atw,
            cond_fok_apply_bid: this.cond_fok_apply_bid,
            cond_fok_spin: this.cond_fok_spin,
            cond_timeout: this.cond_timeout,
            cond_is_oco: this.cond_is_oco,
            cond_is_subject: this.cond_is_subject,
            cond_buy_mid: this.cond_buy_mid,
            cond_buy_best: this.cond_buy_best,
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
    spinNegotiation(user_market) {

        // catch not assigned to a market request yet!
        if(user_market == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market"]));
            });
        }
        
        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+user_market.id+"/market-negotiation",{is_repeat: true})
            .then(response => {
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }

    /**
    *  spin
    */
    killNegotiation() {

        // catch not assigned to a market request yet!
        if(this._user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market"]));
            });
        }
        
        return new Promise((resolve, reject) => {
             axios.delete(axios.defaults.baseUrl +"/trade/user-market/"+this._user_market.id+"/market-negotiation/"+this.id)
            .then(response => {
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
    patchQuote(user_market_request, user_market) {


        if(typeof user_market === 'undefined') {
            // catch not assigned to a market request yet!
            if(this._user_market.id == null) {
                return new Promise((resolve, reject) => {
                    reject(new Errors(["Invalid Market"]));
                });
            }
        }
        user_market_request.id = typeof user_market_request.id !== 'undefined' ? user_market_request.id : this.getUserMarket().getMarketRequest().id;
        

        return new Promise((resolve, reject) => {

             axios.patch(axios.defaults.baseUrl +"/trade/user-market-request/"+user_market_request.id+"/user-market/"+user_market.id, this.prepareStore())
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                // link now that we are saved
                user_market.setMarketRequest(user_market_request);
                user_market.setCurrentNegotiation(this);
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }

    // move through the bid and offer to figure out who set it up first
    getAmountSource(attr)
    {
        let prevItem = null;
        if(this.market_negotiation_id != null)
        {
            prevItem = this.getUserMarket().market_negotiations.find((itItem) => this.market_negotiation_id == itItem.id);
        }
        
        if(typeof prevItem !== "undefined" &&  prevItem != null && prevItem.market_negotiation_id != prevItem.id  && prevItem[attr] == this[attr])
        {
            return prevItem.getAmountSource(attr);   
        }else
        {
            return this;  
        }  
    }

    getLastTradeNegotiation()
    {
        return  this.trade_negotiations.length > 0 ? this.trade_negotiations[this.trade_negotiations.length - 1] : null;
    }

   /**
    *   addNegotiation - add user trade_negotiation
    *   @param {UserMarketNegotiation} trade_negotiation - UserMarketNegotiation objects
    */
    addTradeNegotiation(trade_negotiation) {
        
        if(!(trade_negotiation instanceof TradeNegotiation)) {
            trade_negotiation = new TradeNegotiation(trade_negotiation);
        }

        trade_negotiation.setUserMarketNegotiation(this);
        this.trade_negotiations.push(trade_negotiation);
    }


    /**
    *   addNegotiations - add array of user market_negotiations
    *   @param {Array} market_negotiations - array of UserMarketNegotiation objects
    */
    addTradeNegotiations(trade_negotiations) {
        trade_negotiations.forEach(trade_negotiation => {
            this.addTradeNegotiation(trade_negotiation);
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