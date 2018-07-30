import UserMarketRequest from './UserMarketRequest'
export default class Market {

    constructor(options) {
        this.market_requests = [];
        this.children = [];
        this._parent = null;
        const defaults = {
            id: "",
            title: "",
            description: "",
            order: "",
            market_type_id: "",
            parent_id: "",
            is_displayed: true,
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        // register markets
        if(options && options.market_requests) {
            this.addMarketRequests(options.market_requests);
        }

        // register market children
        if(options && options.children) {
            this.addChildren(options.children);
        }
    }

    /**
    *   addMarketRequest - add user market
    *   @param {UserMarketRequest} market - UserMarket objects
    */
    addMarketRequest(market_req) {
        let is_new_market = true;
        if(!(market_req instanceof UserMarketRequest)) {
            market_req = new UserMarketRequest(market_req);
        }
        for (let i = 0; i < this.market_requests.length; i++) {
            if (this.market_requests[i].id === market_req.id) {
                is_new_market = false;
            }
        }
        if (is_new_market) {
            market_req.setMarket(this);
            this.market_requests.push(market_req);
        }
    }

    /**
    *   updateMarketRequest - updates a current user market
    *   @param {UserMarketRequest} market_req - UserMarket objects
    *   @param {Int} index - UserMarket objects
    *   
    *   @todo make general Market update method that updates all market requests
    */
    updateMarketRequest(market_req, index) {
        console.log("=======================UPDATING MARKET REQUEST=======================");
        console.log("Replace: ", this.market_requests[index]);
        console.log("With: ", market_req);
        console.log("Before: ", this.market_requests);
        this.market_requests[index].update(market_req);
        /*console.log("After: ", this.market_requests);*/
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
    *   addChild - add user market
    *   @param {UserMarketRequest} market - UserMarket objects
    */
    addChild(market) {
        market.setParent(this);
        this.children.push(market);
    }

    /**
    *   addChildren - add array of user market_requests
    *   @param {Array} market_requests - array of UserChild objects
    */
    addChildren(markets) {
        markets.forEach(market => {
            if(!(market instanceof Market)) {
                market = new Market(market);
            }
            this.addChild(market);
        });
    }

    /**
    *   setParent - Set the parent Market
    *   @param {Market} market - Market object
    */
    setParent(market) {
        this._parent = market;
    }

    /**
    *   getParent - Get the parent Market
    *   @return {Market}
    */
    getParent() {
        return this._parent;
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