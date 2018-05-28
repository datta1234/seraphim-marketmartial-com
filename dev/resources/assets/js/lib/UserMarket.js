module.exports = class UserMarket {

    constructor(options) {
        const defaults = {
            market_negotiations: [],
        }

        // internal
        this._market_request = null;
        this._current_market_negotiation = null;

        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && options[key]) {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        // register current
        if(options && options.current_market_negotiation) {
            this.setCurrentNegotiation(options.current_market_negotiation);
        }
    }

    /**
    *   setParent - Set the parent Market
    *   @param {Market} market - Market object
    */
    setParent(market) {
        this._market_request = market;
    }

    /**
    *   getParent - Get the parent Market
    *   @return {Market}
    */
    getParent() {
        return this._market_request;
    }

    /**
    *   addNegotiation - add user market_negotiation
    *   @param {Negotiation} market_negotiation - Negotiation objects
    */
    addNegotiation(market_negotiation) {
        market_negotiation.setUserMarket(this);
        this.market_negotiations.push(market_negotiation);
    }

    /**
    *   addNegotiations - add array of user market_negotiations
    *   @param {Array} market_negotiations - array of Negotiation objects
    */
    addNegotiations(market_negotiations) {
        market_negotiations.forEach(market_negotiation => {
            this.addNegotiation(market_negotiation);
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