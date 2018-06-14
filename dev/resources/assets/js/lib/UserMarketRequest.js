export default class UserMarketRequest {

    constructor(options) {
        // default internal
        this._market = null;
        // default public
        this.trade_items = [];
        this.quotes = [];
        const defaults = {
            id: "",
            trade_structure: "",
            attributes: {
                state: "",
                bid_state: "",
                offer_state: "",
            },
            quote: null,
            chosen_user_market: null,
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

        // register trade_items
        if(options && options.trade_items) {
            this.addTradeItems(options.trade_items);
        }

        // register quotes
        if(options && options.user_market_quotes) {
            this.addUserMarketQuotes(options.user_market_quotes);
        }

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
    *   setUserMarket - Set the UserMarketRequest
    *   @param {UserMarket} user_market - UserMarket object
    */
    setChosenUserMarket(user_market){
        this.chosen_user_market = user_market;
    }

    /**
    *   getChosenUserMarket - get the chosen user market
    *   @return {UserMarket}
    */
    getChosenUserMarket() {
        return this.chosen_user_market;
    }

    /**  TODO LOOK INTO THIS
    *   setChosenUserMarket - set the chosen UserMarket
    *   @param {UserMarket}
    */
    /*setChosenUserMarket(user_market) {
        if(this.user_markets.indexOf(user_market) == -1) {
            this.addUserMarket(user_market);
        }
        this._chosen_user_market = user_market;
    }*/

    /**
    *   getParent - Get the parent Market
    *   @return {Market}
    */
    getParent() {
        return this._market;
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