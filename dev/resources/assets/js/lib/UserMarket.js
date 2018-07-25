import Errors from './Errors';
import UserMarketNegotiation from './UserMarketNegotiation';

export default class UserMarket {

    constructor(options) {
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

    /**
    * toJSON override
    */
    toJSON() {
        let json = {};
        Object.keys(this).forEach(key => {
            if(key[0] != '_') {
                json[key] = this[key];
            }
        });
        return json;
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
    store() {
        // catch not assigned to a market request yet!
        if(this.user_market_request_id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market Request"]));
            });
        }
        return new Promise((resolve, reject) => {
            return axios.post(axios.defaults.baseUrl + "/trade/user-market-request/"+this.user_market_request_id+"/user-market", this.prepareStore())
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
    *   update - updates this User Market
    *   @param {UserMarket} user_market - UserMarket object
    *
    *   @TODO add more complex assignment
    */
    update(user_market) {
        if(user_market !== null){
            Object.entries(user_market).forEach( ([key, value]) => {
                if(Array.isArray(value)) {
                    //call array rebind method
                    this._reassignArray(value, this[key]);

                } else if (value instanceof Object) {
                    //call object rebind method
                    this._reassignObject(value, this[key]);
                } else {
                    this[key] = value;
                }
            });
        }
    }


    _reassignArray(from_arr, to_arr) {
        let is_custom_elem_arr = false;
        to_arr.forEach( (element, index) => {
            if( element instanceof UserMarketNegotiation) {
                is_custom_elem_arr = true;
                element.update(this[key]);
            }
        });
        if(!is_custom_elem_arr) {
            to_arr = from_arr;
        }
    }

    _reassignObject(from_obj, to_obj) {
        if( from_obj instanceof UserMarketNegotiation) {
            from_obj.update(this[key]);
        } else {
            if( !(typeof to_obj == 'undefined') && !(to_obj == null) && !(typeof from_obj == 'undefined') && !(from_obj == null) ) {
                Object.assign(to_obj, from_obj);
            }
        }
    }
}