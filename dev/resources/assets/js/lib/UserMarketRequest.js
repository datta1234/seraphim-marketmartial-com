module.exports = class UserMarketRequest {

    constructor(options) {
        this.user_markets = [];
        this._chosen_user_market = null;
        const defaults = {
            attributes: {
                expiration_date: moment(),
                strike: "",
            }
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && options[key]) {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        // register user_markets
        if(options && options.user_markets) {
            this.addUserMarkets(options.user_markets);
        }

        // register chosen
        if(options && options.chosen_user_market) {
            if(this.user_markets.indexOf(options.chosen_user_market) == -1) {
                this.addUserMarkets(options.user_markets);
            }
            this.setChosenUserMarket(options.chosen_user_market);
        }
    }

    /**
    *   addUserMarket - add user market
    *   @param {UserMarket} user_market - UserMarket objects
    */
    addUserMarket(user_market) {
        user_market.setParent(this);
        this.user_markets.push(user_market);
    }

    /**
    *   addUserMarkets - add array of user user_markets
    *   @param {Array} user_markets - array of UserMarket objects
    */
    addUserMarkets(user_markets) {
        user_markets.forEach(user_market => {
            this.addUserMarket(user_market);
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
    *   setChosenUserMarket - set the chosen UserMarket
    *   @param {UserMarket}
    */
    setChosenUserMarket(user_market) {
        if(this.user_markets.indexOf(user_market) == -1) {
            this.addUserMarket(user_market);
        }
        this._chosen_user_market = user_market;
    }

    /**
    *   getChosenUserMarket - get the chosen user market
    *   @return {UserMarket}
    */
    getChosenUserMarket() {
        return this._chosen_user_market;
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