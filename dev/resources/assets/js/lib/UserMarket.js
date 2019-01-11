import BaseModel from './BaseModel';
import Errors from './Errors';
import UserMarketNegotiation from './UserMarketNegotiation';
import UserMarketVolatility from '~/lib/UserMarketVolatility';
import ActiveCondition from './ActiveCondition';
import SentCondition from './SentCondition';

export default class UserMarket extends BaseModel {

    constructor(options) {

       super({
            _used_model_list: [UserMarketNegotiation],
            _relations:{
                market_negotiations:{
                    addMethod: (market_negotiation) => { this.addNegotiation(market_negotiation) },
                },
                active_conditions: {
                    setMethod: (active_condition) => { this.setActiveConditions(active_condition) },
                },
                sent_conditions: {
                    setMethod: (sent_condition) => { this.setSentConditions(sent_condition) },
                },
                activity: {
                    setMethod: (activity) => { this.setActivity(activity) },
                },
                trading_at_best: {
                    setMethod: (negotiation) => { this.setTradingAtBest(negotiation) },
                },
                volatilities: {
                    addMethod: (volatility) => { this.addVolatility(volatility) },
                    setMethod: (volatility) => { this.setVolatility(volatility) },
                }
            }
        });

        // default internal
        this._user_market_request = null;
        // default public
        this.market_negotiations = [];
        const defaults = {
            id: "",
            status: "",
            is_watched: false,
            user_market_request_id: null,
            current_market_negotiation: null,
            created_at: moment(),
            updated_at: moment(),

            // optional
            user: null,
            org: null,
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

        // register market_negotiations

        if(options && options.market_negotiations) {
            this.addNegotiations(options.market_negotiations);
        }

        this.volatilities = [];
        if(options && options.volatilities) {
            this.setVolatilities(options.volatilities);
        }

        this.active_conditions = [];
        if(options && options.active_conditions) {
            this.setActiveConditions(options.active_conditions);
        }

        this.sent_conditions = [];
        if(options && options.sent_conditions) {
            this.setSentConditions(options.sent_conditions);
        }

        this.activity = {};
        if(options && options.activity) {
            this.setActivity(options.activity);
        }

        this.trading_at_best = null;
        if(options && options.trading_at_best) {
            this.setTradingAtBest(options.trading_at_best);
        }   

    }

    /**
    *   setMarketRequest - Set the parent UserMarketRequest
    *   @param {UserMarketRequest} user_market_request - UserMarketRequest object
    */
    setMarketRequest(user_market_request) {
        this.user_market_request_id = user_market_request.id;
        this._user_market_request = user_market_request;
    }

    /**
    *   getMarketRequest - Get the parent UserMarketRequest
    *   @return {UserMarketRequest}
    */
    getMarketRequest() {
        return this._user_market_request;
    }

    /**
    *   setTradingAtBest - Set the TradeAtBestOpenRequest
    *   @param {UserMarket} user_market - UserMarket object
    */
    setTradingAtBest(negotiation) {
     
        if(!(negotiation instanceof UserMarketNegotiation)) {
            negotiation = new UserMarketNegotiation(negotiation);
        }
        negotiation.setUserMarket(this);
        this.trading_at_best = negotiation;
    }

    /**
    *   isTradeAtBestOpen - get the chosen user market
    *   @return {UserMarket}
    */
    isTradingAtBest() {
        return typeof this.trading_at_best != undefined && this.trading_at_best != null;
    }

    /**
    *   addNegotiation - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    addNegotiation(user_market_negotiation) {
        
        if(!(user_market_negotiation instanceof UserMarketNegotiation)) {
            user_market_negotiation = new UserMarketNegotiation(user_market_negotiation);
        }

        user_market_negotiation.setUserMarket(this);
        this.market_negotiations.push(user_market_negotiation);
    }

    /**
    *   setActiveConditions - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    setActiveConditions(active_conditions) {
        this.active_conditions.splice(0, this.active_conditions.length);
        active_conditions.forEach(cond => {
            this.addActiveCondition(cond);
        });
    }

    /**
    *   setSentConditions - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    setSentConditions(sent_conditions) {
        this.sent_conditions.splice(0, this.sent_conditions.length);
        sent_conditions.forEach(cond => {
            this.addSentCondition(cond);
        });
    }

    /**
    *   setVolatility - set the volatility colelction
    *   @param {Object} volatility - Volatility object
    */
    addVolatility(volatility) {
        if(!(volatility instanceof UserMarketVolatility)) {
            volatility = new UserMarketVolatility(volatility);
        }
        volatility.setUserMarket(this);
        this.volatilities.push(volatility);
        console.log("Added Volatility", volatility);
    }

    /**
    *   setVolatility - set the volatility colelction
    *   @param {Object} volatility - Volatility object
    */
    setVolatilities(volatilities) {
        this.volatilities.splice(0, this.volatilities.length);
        volatilities.forEach(vol => {
            this.addVolatility(vol);
        });
    }

    volatilityForGroup(group_id) {
        return this.volatilities.find(x => x.group_id == group_id);
    }

    /**
    *   addActiveCondition - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    addActiveCondition(active_condition) {
        
        if(!(active_condition instanceof ActiveCondition)) {
            active_condition = new ActiveCondition(this, active_condition);
        }

        this.active_conditions.push(active_condition);
    }

    /**
    *   addActiveCondition - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    addSentCondition(sent_condition) {
        
        if(!(sent_condition instanceof SentCondition)) {
            sent_condition = new SentCondition(this, sent_condition);
        }

        this.sent_conditions.push(sent_condition);
    }

    /**
    *   setActivity - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    setActivity(activity) {
        this.activity = activity;
    }

    /**
    *   addNegotiations - add array of user market_negotiations
    *   @param {Array} market_negotiations - array of UserMarketNegotiation objects
    */
    addNegotiations(user_market_negotiations) {
        user_market_negotiations.forEach(user_market_negotiation => {
            this.addNegotiation(user_market_negotiation);
        });
    }

    /**
    *   setCurrentNegotiation - set the chosen UserMarket
    *   @param {UserMarket}
    */
    setCurrentNegotiation(negotiation) {
        if(!(negotiation instanceof UserMarketNegotiation)) {
            negotiation = new UserMarketNegotiation(negotiation);
        }

        if(this.market_negotiations.indexOf(negotiation) == -1) {
            this.addNegotiation(negotiation);
        }

        
        
        this.current_market_negotiation = negotiation;
    }

    /**
    *   getCurrentNegotiation - get the chosen user market
    *   @return {UserMarket}
    */
    getCurrentNegotiation() {
        return this.current_market_negotiation;
    }


    getLastNegotiation() {
        return this.market_negotiations.length > 0 ? this.market_negotiations[this.market_negotiations.length - 1] : null;
    }

    prepareStore() {
        return {
            user_market_request_id: this.user_market_request_id,
            current_market_negotiation: this.current_market_negotiation.prepareStore(),
            volatilities: this.volatilities.map(x => x.prepareStore())
        };
    }


    /**
    *  store
    */
    store(market_request) {
        // catch not assigned to a market request yet!
        if(market_request.id == null) {
            return new Promise((resolve, reject) => {
                console.log("error man",market_request);
                reject(new Errors("Invalid Market Request"));
            });
        }

        return new Promise((resolve, reject) => {
            axios.post(axios.defaults.baseUrl + "/trade/user-market-request/"+market_request.id+"/user-market", this.prepareStore())
            .then(response => {
               resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });
    }

    
    /**
    *  delete
    */
    delete() {
        // catch not assigned to a market request yet!
        if(this.user_market_request_id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market Request"));
            });
        }
        return new Promise((resolve, reject) => {
            return axios.delete(axios.defaults.baseUrl + "/trade/user-market-request/"+this.user_market_request_id+"/user-market/"+this.id)
            .then(response => {
               resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });

    }


    /**
    * Dismiss this organisation activity
    */
    dismissActivity(activity) {
        // make a . notation string
        activity = activity instanceof Array ? activity.join('.') : activity;
        console.log(activity);

        return new Promise((resolve, reject) => {
            return axios.delete(axios.defaults.baseUrl + "/trade/user-market/"+this.id+"/activity/"+activity)
            .then(response => {
                console.log("ACT:", response);
                this.setActivity(response.data.data.activity);
            })
            .catch(err => {
                reject(err);
            });
        });
    }
}