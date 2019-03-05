import BaseModel from './BaseModel';
import Errors from './Errors';
import UserMarketVolatility from '~/lib/UserMarketVolatility';

export default class UserMarketQuote extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: [],
            _relations:{
                volatilities: {
                    addMethod: (volatility) => { this.addVolatility(volatility) },
                    setMethod: (volatility) => { this.setVolatility(volatility) },
                }
            }
        });

        // default internal
        this._user_market_request = null;
        // default public
        const defaults = {
            id: "",
            is_maker: false,
            is_interest: false,
		    bid_only: false,
		    offer_only: false,
		    vol_spread: null,
		    time: "",

            bid_qty: null,
            bid: null,
            offer: null,
            offer_qty: null,
            is_on_hold: false,
            is_repeat: false,

            // optional
            user: null,
            org: null,
            market_request_summary: null,
            org_interest: null,
            org_maker: null,
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        this.volatilities = [];
        if(options && options.volatilities) {
            this.setVolatilities(options.volatilities);
        }
    }

    /**
    *   setParent - Set the parent UserMarketRequest
    *   @param {UserMarketRequest} user_market_request - UserMarketRequest object
    */
    setMarketRequest(user_market_request) {
        this._user_market_request = user_market_request;
    }

    /**
    *   getParent - Get the parent UserMarketRequest
    *   @return {UserMarketRequest}
    */
    getMarketRequest() {
        return this._user_market_request;
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

    putOnHold() {
        // catch not assigned to a user market request yet!
        if(this._user_market_request == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market Request"));
            });
        }
        return new Promise((resolve, reject) => {
           axios.patch(axios.defaults.baseUrl + '/trade/user-market-request/'+this._user_market_request.id+'/user-market/'+this.id, {'is_on_hold': true})
            .then(response => {
                this.runActionTaken();
                resolve(response);
            })
            .catch(err => {
                reject(err);
            }); 
        });
    }

    accept() {
        // catch not assigned to a user market request yet!
        if(this._user_market_request == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market Request"));
            });
        }
        return new Promise((resolve, reject) => {
           axios.patch(axios.defaults.baseUrl + '/trade/user-market-request/'+this._user_market_request.id+'/user-market/'+this.id, {'accept': true})
            .then(response => {
                this.runActionTaken();
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
        if(this._user_market_request == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors("Invalid Market Request"));
            });
        }
        return new Promise((resolve, reject) => {
            return axios.delete(axios.defaults.baseUrl + "/trade/user-market-request/"+this._user_market_request.id+"/user-market/"+this.id)
            .then(response => {
                resolve(response);
            })
            .catch(err => {
                reject(err);
            });
        });
    }

    runActionTaken() {
        if(this._user_market_request) {
            this._user_market_request.runActionTaken();
        }
    }
}