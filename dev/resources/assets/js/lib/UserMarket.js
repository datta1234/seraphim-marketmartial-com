import BaseModel from './BaseModel';
import Errors from './Errors';
import UserMarketNegotiation from './UserMarketNegotiation';
import ActiveCondition from './ActiveCondition';

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
                activity: {
                    setMethod: (activity) => { this.setActivity(activity) },
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
            user_market_request_id: null,
            current_market_negotiation: null,
            created_at: moment(),
            updated_at: moment(),
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

        this.active_conditions = [];
        if(options && options.active_conditions) {
            this.setActiveConditions(options.active_conditions);
        }

        this.activity = {};
        if(options && options.activity) {
            this.setActivity(options.activity);
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
                reject(new Errors(["Invalid Market Request"]));
            });
        }

        return new Promise((resolve, reject) => {
            axios.post(axios.defaults.baseUrl + "/trade/user-market-request/"+market_request.id+"/user-market", this.prepareStore())
            .then(response => {
               resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }


    noFutherCares()
    {
        console.log(this.user_market_request_id,this.id);
        // catch not assigned to a market request yet!
        if(this.user_market_request_id == null) {
            return new Promise((resolve, reject) => {
                console.log("error man",this.user_market_request_id);
                reject(new Errors(["Invalid Market Request"]));
            });
        }

         return new Promise((resolve, reject) => {
            axios.post(axios.defaults.baseUrl + "/trade/user-market-request/"+this.user_market_request_id+"/user-market/"+this.id+"/no-further-cares")
            .then(response => {
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
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
                reject(new Errors(["Invalid Market Request"]));
            });
        }
        return new Promise((resolve, reject) => {
            return axios.delete(axios.defaults.baseUrl + "/trade/user-market-request/"+this.user_market_request_id+"/user-market/"+this.id)
            .then(response => {
               resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
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
                if(response.data.success) {
                    this.setActivity(response.data.activity);
                }
                resolve(response.data);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            });
        });
    }
}