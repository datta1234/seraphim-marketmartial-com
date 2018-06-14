export default class Market {

    constructor(options) {
        this.market_requests = [];
        const defaults = {
            id: "",
            title: "",
            description: "",
            order: "",
            market_type_id: "",

        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && options[key]) {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        // register markets
        if(options && options.market_requests) {
            this.addMarketRequests(options.market_requests);
        }
    }

    /**
    *   addMarketRequest - add user market
    *   @param {UserMarketRequest} market - UserMarket objects
    */
    addMarketRequest(market_req) {
        market_req.setParent(this);
        this.market_requests.push(market_req);
    }

    /**
    *   addMarketRequests - add array of user market_requests
    *   @param {Array} market_requests - array of UserMarketRequest objects
    */
    addMarketRequests(market_requests) {
        market_requests.forEach(market_req => {
            this.addMarketRequest(market_req);
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