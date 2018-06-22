import Errors from './Errors';
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

        return axios.post("/trade/market-request/"+this.user_market_request_id+"/user-market", this.prepareStore())
        .then(response => {
            console.log(response);
            return response;
        })
        .catch(err => {
            console.error(err);
            return new Errors(err);
        });
    }
}