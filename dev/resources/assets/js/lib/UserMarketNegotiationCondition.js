export default class UserMarketNegotiationCondition {

    constructor(options) {
        // default internal
        this._user_market_negotiation = null;
        // default public
        const defaults = {
            id: "",
            title: "",
            alias: "",
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
    }

    /**
    *   setUserMarketNegotiation - Sets the conditions UserMarketNegotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation for the condition
    */
    setUserMarketNegotiation(user_market_negotiation) {
        this._user_market_negotiation = user_market_negotiation;
    }

    /**
    *   getUserMarketNegotiation - Gets the conditions  UserMarketNegotiation
    *   @return {UserMarketNegotiation}
    */
    getUserMarketNegotiation() {
        return this._user_market_negotiation;
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

    prepareStore() {
        return {
            id: this.id,
            title: this.title,
            alias: this.alias,
        };
    }

}