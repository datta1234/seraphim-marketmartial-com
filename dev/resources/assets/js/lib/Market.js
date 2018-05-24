module.exports = class Market {

    constructor(options) {
        this.markets = [];
        const defaults = {
            title: "",
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
        if(options.markets) {
            this.addUserMarkets(options.markets);
        }
    }

    /**
    *   addUserMarket - add user market
    *   @param {UserMarket} market - UserMarket objects
    */
    addUserMarket(market) {
        market.setParent(this);
        this.markets.push(market);
    }

    /**
    *   addUserMarkets - add array of user markets
    *   @param {Array} markets - array of UserMarket objects
    */
    addUserMarkets(markets) {
        markets.forEach(market => {
            this.addUserMarket(market);
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