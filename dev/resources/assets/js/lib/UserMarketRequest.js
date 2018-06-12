module.exports = class UserMarketRequest {

    constructor(options) {
        // default internal
        this._market = null;
        // default public
        this.trade_items: []
        this.quotes = [];
        this.quote = null;
        this.user_market = null;
        const defaults = {
            id: "",
            trade_structure: "",
            attributes: {
                state: "",
                bid_state: "",
                offer_state: "",
            },
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

        // register user_markets
        if(options && options.user_markets) {
            this.addUserMarkets(options.user_markets);
        }

        // register quotes
        if(options && options.user_market_quotes) {
            this.addUserMarketQuotes(options.user_market_quotes);
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
    *   addUserMarketQuote - add user market quote
    *   @param {UserMarketQuote} user_market_quote - UserMarketQuote objects
    */
    addUserMarketQuote(user_market_quote) {
        user_market_quote.setParent(this);
        this.quotes.push(user_market_quote);
    }

    /**
    *   addUserMarketQuotes - add array of user user_market_quotes
    *   @param {Array} user_market_quotes - array of UserMarketQuote objects
    */
    addUserMarketQuotes(user_market_quotes) {
        user_market_quotes.forEach(user_market_quote => {
            this.addUserMarketQuote(user_market_quote);
        });
    }

    /**
    *   setUserMarketQuote - Set the UserMarketRequest quote
    *   @param {UserMarketQuote} user_market_quote - UserMarketQuote object
    */
    setUserMarketQuote(user_market_quote){
        this.quote = user_market_quote;
    }

    /**
    *   addTradeItem - add trade item
    *   @param {} trade_item - trade item object
    */
    addTradeItem(trade_item) {
        this.trade_items.push(trade_item);
    }

    /**
    *   addTradeItems - add array of trade items
    *   @param {Array} trade_items - array of trade item objects
    */
    addTradeItems(trade_items) {
        trade_items.forEach(trade_item => {
            this.addTradeItem(trade_item);
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