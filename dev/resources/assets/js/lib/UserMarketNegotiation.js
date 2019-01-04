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
               },
               condition: {
                    setMethod: (cond) => { this.setActiveCondition(cond) },
               }
            }
        });

        // default internal
        this._user_market = null;

           // default public
        this.trade_negotiations = [];

        const defaults = {
            id: null,
            bid: "",
            offer: "",
            bid_source: "",
            offer_source: "",
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
            is_killed: false,
            cond_is_repeat_atw: null,
            cond_fok: null, // alias
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
            time: null,
            applicable_timeout: 0,
            creation_idx: null,
            created_at: moment(),

            // optional
            bid_org: null,
            offer_org: null,
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
        console.log("options in the user_market_negotiation",options);

        this._active_condition = null;
        if(options && options['active_condition']) {
            this.setActiveCondition(options['active_condition']);
        }

        this._sent_condition = null;
        if(options && options['sent_condition']) {
            this.setSentCondition(options['sent_condition']);
        }
    }


    setActiveCondition(cond) {
        this._active_condition = cond;
    }

    setSentCondition(cond) {
        this._sent_condition = cond;
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

    prepareAmend(user_market) {
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
            volatilities: user_market.volatilities.map(x => x.prepareStore())
        };
    }

    getTimeoutRemaining() {
        let diff = moment(this.created_at).add(this.applicable_timeout, 'minutes').diff(moment());
        // ensure its not shown if its timed out
        if(diff < 0) {
            return "00:00";
        } else {
            return moment.duration(diff).format("mm:ss");
        }
    }

    hasTimeoutRemaining() {
        let diff = moment(this.created_at).add(this.applicable_timeout, 'minutes').diff(moment());
        // ensure its not shown if its timed out
        return diff > 0;
    }

    storeWorkBalance(user_market_request,user_market,quantity) {
        // catch not assigned to a market request yet!
        if(user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market"));
            });
        }
 
        return new Promise((resolve, reject) => {

             axios.post(axios.defaults.baseUrl + "/trade/user-market-request/"+user_market_request.id+"/user-market/"+user_market.id+"/work-the-balance", {quantity:quantity})
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });
    }

    /**
    *  store
    */
    storeNegotiation(user_market) {
        console.log("Store");
        // catch not assigned to a market request yet!
        if(user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market"));
            });
        }
     
        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+user_market.id+"/market-negotiation", this.prepareStore())
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });
    }

    /**
    *  amend
    */
    amendNegotiation(user_market) {
        console.log("Amending "+this.id, this);
        // catch not assigned to a market request yet!
        if(user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market"));
            });
        }

        if(this.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Negotiation"));
            });   
        }
     
        return new Promise((resolve, reject) => {
             axios.put(axios.defaults.baseUrl +"/trade/user-market/"+user_market.id+"/market-negotiation/"+this.id, this.prepareStore())
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });
    }

    /**
    *  counter
    */
    counterNegotiation(counter_market_negotiation) {
        
        // catch not assigned to a market request yet!
        if(this.getUserMarket().id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market"));
            });
        }

        // catch not created yet
        if(this.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Negotiation"));
            });
        }
        // set to proposal by default (happens on server too hint type)
        counter_market_negotiation.is_private = true;
        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+this.getUserMarket().id+"/market-negotiation/"+this.id+"/counter", counter_market_negotiation.prepareStore())
            .then(resolve)
            .catch(err => {
                reject(err);
            });
        });
    }


    /**
    *  improve trade at best
    */
    improveBestNegotiation(counter_market_negotiation) {
        
        // catch not assigned to a market request yet!
        if(this.getUserMarket().id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market"));
            });
        }

        // catch not created yet
        if(this.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Negotiation"));
            });
        }
        // set to proposal by default (happens on server too hint type)
        counter_market_negotiation.is_private = true;
        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+this.getUserMarket().id+"/market-negotiation/"+this.id+"/improve", counter_market_negotiation.prepareStore())
            .then(resolve)
            .catch(err => {
                reject(err);
            });
        });
    }
    
    
    getQuantityType()
    {
       if(this.getUserMarket() && this.getUserMarket().getMarketRequest() && this.getUserMarket().getMarketRequest().getMarket())
        {
            return this.getUserMarket().getMarketRequest().getMarket().title == "SINGLES" ? "Rm" : "Contracts";
        }
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
                reject(new Errors("Invalid Market"));
            });
        }
        
        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+user_market.id+"/market-negotiation",{is_repeat: true})
            .then(response => {
                resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });
    }

    /**
    *  repeat
    */
    repeatNegotiation() {

        // catch not assigned to a market request yet!
        if(!this._user_market || this._user_market.id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market"));
            });
        }

        if(!this.id) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Negotiation"));
            });
        }
        
        return new Promise((resolve, reject) => {
             axios.post(axios.defaults.baseUrl +"/trade/user-market/"+this._user_market.id+"/market-negotiation/"+this.id+"/repeat",{is_repeat: true})
            .then(response => {
                resolve(response);
            })
            .catch(err => {
                reject(err);
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
                reject(new Errors("Invalid Market"));
            });
        }
        
        return new Promise((resolve, reject) => {
             axios.delete(axios.defaults.baseUrl +"/trade/user-market/"+this._user_market.id+"/market-negotiation/"+this.id)
            .then(response => {
                resolve(response);
            })
            .catch(err => {
                reject(err);
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
                    reject(new Errors("Invalid Market"));
                });
            }
        }
        user_market_request.id = typeof user_market_request.id !== 'undefined' ? user_market_request.id : this.getUserMarket().getMarketRequest().id;
        

        return new Promise((resolve, reject) => {

             axios.patch(axios.defaults.baseUrl +"/trade/user-market-request/"+user_market_request.id+"/user-market/"+user_market.id, this.prepareAmend(user_market))
            .then(response => {
                response.data.data = new UserMarketNegotiation(response.data.data);
                // link now that we are saved
                user_market.setMarketRequest(user_market_request);
                user_market.setCurrentNegotiation(this);
                resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });
    }

    // move through the bid and offer to figure out who set it up first
    getAmountSource(attr)
    {
        let prevItem = null;
        if(this.market_negotiation_id != null)
        {
            if(this._active_condition != null) {
                prevItem = this._active_condition.history.find((itItem) => this.market_negotiation_id == itItem.id);
            } else if (this._sent_condition != null) {
                prevItem = this._sent_condition.history.find((itItem) => this.market_negotiation_id == itItem.id);
            } else {
                prevItem = this.getUserMarket().market_negotiations.find((itItem) => this.market_negotiation_id == itItem.id);
            }
        }
        
        if(typeof prevItem !== "undefined" &&  prevItem != null  && prevItem[attr] == this[attr])
        {
            return prevItem.getAmountSource(attr);   
        }else
        {
            return this;  
        }  
    }

    getLastTradeNegotiation()
    {
        if(this.trade_negotiations.length > 0)
        {
         let lastNegotiation = this.trade_negotiations[this.trade_negotiations.length - 1];
         lastNegotiation.setUserMarket(this);
         return lastNegotiation;
        }
        else
        {
            return null;
        }
    }


    getFirstTradeNegotiation()
    {
        if(this.trade_negotiations.length > 0)
        {
         let firstNegotiation = this.trade_negotiations[0];
         firstNegotiation.setUserMarket(this);
         return firstNegotiation;
        }
        else
        {
            return null;
        }
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
                reject(new Errors("Invalid Market"));
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
                reject(err);
            });
        });
    }

    get ratio() {
        return this.bid_qty / this.offer_qty;
    }

    get parent_negotiation() {
        return this._user_market.market_negotiations.find(x => x.id == this.market_negotiation_id);
    }

    get level_sides() {
        let bid_source = this.getAmountSource('bid');
        let offer_source = this.getAmountSource('offer');
        let sides = [];
        // im on the bid
        if(bid_source.is_my_org) {
            sides.push('bid');
        }
        if(offer_source.is_my_org) {
            sides.push('offer');
        }
        return sides;
    }
    
    /**
    *   test if the parents are spun
    */
    isSpun() {
        return this.is_repeat && this.parent_negotiation && this.parent_negotiation.is_repeat;
    }

    /**
    *   test if the negotiation has been traded
    */
    isTraded() {
        let lastTrade = this.getLastTradeNegotiation();
        return lastTrade && lastTrade.traded;
    }

    /**
    *   test if the negotiation is trading
    */
    isTrading() {
        let lastTrade = this.getLastTradeNegotiation();
        return lastTrade && !lastTrade.traded;
    }

    /**
    * determine if there is a condition applied
    */
    hasCondition() {
        let conds = {
            is_private: false,
            cond_is_repeat_atw: null,
            cond_fok: null, // alias
            cond_fok_apply_bid: null,
            cond_fok_spin: null,
            cond_timeout: null,
            cond_is_oco: null,
            cond_is_subject: null,
            cond_buy_mid: null,
            cond_buy_best: null,
        };
        for(let key in conds) {
            if(this[key] != conds[key]) {
                return true;
            }
        }
        return false;
    }

}