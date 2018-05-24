module.exports = class UserMarket {

    constructor(options) {
        const defaults = {
            date: "",
            strike: "",
            bid: "",
            offer: "",
            state: '',
            market_negotiations: [],
            // internal
            _market: null,
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && options[key]) {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });
    }

    /**
    *   setParent - Set the parent Market
    *   @param {Market} market - Market object
    */
    setParent(market) {
        this._market = market;
    }

    /**
    *   getParent - Get the parent Market
    *   @return {Market}
    */
    getParent() {
        return this._market;
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