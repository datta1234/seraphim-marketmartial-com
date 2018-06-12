module.exports = class UserMarket {

    constructor(options) {
        // default internal
        this._current_market_negotiation = null;
        this._user_market_request = null;
        // default public
        this.market_negotiations: [];
        const defaults = {
            id: "",
            status: "",
            created_at: moment(),
        }

        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && options[key]) {
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
    *   setParent - Set the parent UserMarketRequest
    *   @param {UserMarketRequest} user_market_request - UserMarketRequest object
    */
    setParent(user_market_request) {
        this._user_market_request = user_market_request;
    }

    /**
    *   getParent - Get the parent UserMarketRequest
    *   @return {UserMarketRequest}
    */
    getParent() {
        return this._user_market_request;
    }

    /**
    *   addNegotiation - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    addNegotiation(user_market_negotiation) {
        user_market_negotiation.setParent(this);
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
        this._current_market_negotiation = negotiation;
    }

    /**
    *   getCurrentNegotiation - get the chosen user market
    *   @return {UserMarket}
    */
    getCurrentNegotiation() {
        return this._current_market_negotiation;
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

}