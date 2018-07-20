export default class UserMarketQuote {

    constructor(options) {
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
            is_on_hold: false
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });
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

    putOnHold() {
        
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