export default class UserMarketQuote {

    constructor(options) {
        // default internal
        this._user_market_request = null;
        // default public
        const defaults = {
            id: "",
		    bid_only: false,
		    offer_only: false,
		    vol_spread: "",
		    created_at: moment()
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