module.exports = class UserMarketNegotiation {

    constructor(options) {
        // default internal
        this._user_market = null;
        // default public
        this.conditions: [];
        const defaults = {
            bid: "",
            offer: "",
            bid_qty: 0,
            offer_qty: 0,
            bid_premium: "",
            offer_premium: "",
            is_put: "",
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

        // register conditions
        if(options && options.user_market_negotiation_condition) {
            this.addUserMarketNegotiationConditions(options.user_market_negotiation_condition);
        }
    }

    /**
    *   setParent - Sets the negotiations UserMarket
    *   @param {UserMarket} market - UserMarket for the negotiation
    */
    setParent(user_market) {
        this._user_market = user_market;
    }

    /**
    *   getParent - Gets the negotiations UserMarket
    *   @return {UserMarket}
    */
    getParent() {
        return this._user_market;
    }

    /**
    *   addNegotiation - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    addUserMarketNegotiationCondition(user_market_negotiation_condition) {
        user_market_negotiation_condition.setParent(this);
        this.conditions.push(user_market_negotiation_condition);
    }

    /**
    *   addNegotiations - add array of user market_negotiations
    *   @param {Array} market_negotiations - array of UserMarketNegotiation objects
    */
    addUserMarketNegotiationConditions(user_market_negotiation_conditions) {
        user_market_negotiation_conditions.forEach(user_market_negotiation_condition => {
            this.addUserMarketNegotiationCondition(user_market_negotiation_condition);
        });
    }

    /**
    * toJSON - override removing internal references
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