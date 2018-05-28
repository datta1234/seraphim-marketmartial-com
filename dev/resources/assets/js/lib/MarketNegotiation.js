module.exports = class MarketNegotiation {

    constructor(options) {
        // default internal
        this._market = null;
        // default public
        const defaults = {
            bid_qty: 0,
            bid: null,
            offer: null,
            offer_qty: 0,
            time: null
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
        if(options && options.market) {
            this.setUserMarket(options.market);
        }
    }

    /**
    *   setUserMarket - Sets the negotiations UserMarket
    *   @param {UserMarket} market - UserMarket for the negotiation
    */
    setUserMarket(market) {
        this._market = market;
    }

    /**
    *   getUserMarket - Gets the negotiations UserMarket
    *   @return {UserMarket}
    */
    getUserMarket() {
        return this._market;
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