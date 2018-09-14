import BaseModel from './BaseModel';
import UserMarket from './UserMarket';
import UserMarketQuote from './UserMarketQuote';
import Errors from './Errors';

export default class UserMarketRequest extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: [UserMarket,UserMarketQuote],
            _relations:{
                quotes:{
                    addMethod: (quote) => { this.addUserMarketQuote(quote) },
                },
                sent_quote: {
                    setMethod: (sent_quote) => { this.setSentQuote(sent_quote) },
                },
                chosen_user_market: {
                    setMethod: (user_market) => { this.setChosenUserMarket(user_market) },
                }
            }
        });

        this._endpoint = ["/trade/market/", "/"];
        
        // default internal
        this._market = null;
        // default public
        this.trade_items = {};//with group title as key
        this.quotes = [];
        this.sent_quote = null;
        const defaults = {
            id: "",
            trade_structure: "",
            market_id:"",
            is_interest:false,
            is_market_maker: false,
            attributes: {
                state: "",
                bid_state: "",
                offer_state: "",
                action_needed: false,
                is_interest: false
            },
            quote: null,
            chosen_user_market: null,
            created_at: moment(),
            updated_at: moment(),
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
                if(defaults[key] instanceof moment) {
                    this[key] = moment(this[key]);
                }
            } else {
                this[key] = defaults[key];
            }
        });

        // register trade_items
        if(options && options.trade_items) {
            this.addTradeItems(options.trade_items);
        }

        // register quotes
        if(options && options.quotes) {
            this.addUserMarketQuotes(options.quotes);
        }

        // register sent_quote
        if(options && options.sent_quote) {
            this.setSentQuote(options.sent_quote);
        }

       // register user_market
        if(options && options.chosen_user_market) {
            this.setChosenUserMarket(options.chosen_user_market);
        }

         // register user_market
        if(options && options.user_market) {
            this.setUserMarket(options.user_market);
        }
    }

    /**
    *   addUserMarketQuote - add user market quote
    *   @param {UserMarketQuote} user_market_quote - UserMarketQuote objects
    */
    addUserMarketQuote(user_market_quote) {
        if(!(user_market_quote instanceof UserMarketQuote)) {
            user_market_quote = new UserMarketQuote(user_market_quote);
        }
        user_market_quote.setMarketRequest(this);
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
    setChosenUserMarket(user_market) {   
     
     if(!(user_market instanceof UserMarket)) {
            user_market = new UserMarket(user_market);
        }
        user_market.setMarketRequest(this);
        this.chosen_user_market = user_market;
    }

    /**
    *   getChosenUserMarket - get the chosen user market
    *   @return {UserMarket}
    */
    getChosenUserMarket() {
        return this.chosen_user_market;
    }

    /**
    *   setUserMarketQuote - Set the UserMarketRequest quote
    *   @param {UserMarketQuote} user_market_quote - UserMarketQuote object
    */
    setUserMarketQuote(user_market_quote){
        user_market_quote.setMarketRequest(this);
        this.quote = user_market_quote;
    }

    /**
    *   setUserMarket - Set the UserMarketRequest setSentQuote
    *   @param {UserMarket} user_market - UserMarket object
    */
    setSentQuote(sent_quote) {
        if(!(sent_quote instanceof UserMarket)) {
            sent_quote = new UserMarket(sent_quote);
        }
        sent_quote.setMarketRequest(this);
        this.sent_quote = sent_quote;
    }

    /**
    *   setUserMarket - Set the UserMarketRequest setSentQuote
    *   @param {UserMarket} user_market - UserMarket object
    */
    setUserMarket(user_market) {
        if(!(user_market instanceof UserMarket)) {
            user_market = new UserMarket(user_market);
        }

        user_market.setMarketRequest(this);
        this.user_market = user_market;
    }


    /**
    *   getSentQuote - Set the UserMarketRequest UserMarker
    *   @param {UserMarket} user_market - UserMarket object
    */
    getChosenUserMarket() {
        return this.user_market;
    }

    /**
    *   getUserMarket - Set the UserMarketRequest UserMarker
    *   @param {UserMarket} user_market - UserMarket object
    */
    getSentQuote() {
        return this.sent_quote;
    }

    /**
    *   addTradeItem - add trade item
    *   @param {} trade_item - trade item object
    */
    addTradeItem(group, title, value) {
        if(!this.trade_items[group]) {
            this.trade_items[group] = {};
        }
        this.trade_items[group][title] = value;
    }

    /**
    *   addTradeItems - add array of trade items
    *   @param {Array} trade_items - array of trade item objects
    */
    addTradeItems(trade_items) {
        Object.keys(trade_items).forEach(trade_group => {

            Object.keys(trade_items[trade_group]).forEach(title => {
                this.addTradeItem(trade_group, title, trade_items[trade_group][title]);
            });

        });
    }

    /**
    *   setMarket - Set the parent Market
    *   @param {Market} market - Market object
    */
    setMarket(market) {
        this._market = market;
    }

    /**
    *   getMarket - Get the parent Market
    *   @return {Market}
    */
    getMarket() {
        return this._market;
    }

    /**
    *   actionTaken - Alerts the server that an action has been taken on a Market Request
    *   @return response from the request or the error
    */
    actionTaken() {
        if(!this.attributes.action_needed) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["No actions needed"]));
            });
        }
        return new Promise((resolve, reject) => {
           axios.post(axios.defaults.baseUrl + '/trade/user-market-request/'+ this.id +'/action-taken', {'action_needed': false})
            .then(response => {
                this.attributes.action_needed = response.data.data.action_needed;
                resolve(response);
            })
            .catch(err => {
                reject(new Errors(err.response.data));
            }); 
        });
    }

    /**
    *   canNegotiate - Checks to see if the user can take any negotiation on the current ste of the request
    *   @return response from the request or the error
    */
    canNegotiate()
    {
        let tradebleStatuses = [
                "REQUEST-SENT",
                "REQUEST",
                "REQUEST-SENT-VOL",
                "REQUEST-VOL",
                "NEGOTIATION-VOL",
                "NEGOTIATION-OPEN-VOL"
            ];

        console.log(this.attributes.calc_state);
        console.log(this.attributes.calc_roles);

        return  tradebleStatuses.indexOf(this.attributes.state) > -1;
    }

    /**
    *   canApplyNoCares - Checks to see if the user can apply no cares on the market request 
    *   @return response from the request or the error
    */
    canApplyNoCares()
    {
        //all the markets you cant disregard     
       let cantDisRegard = [
                "NEGOTIATION-VOL",
                "REQUEST-SENT-VOL",
                "REQUEST-VOL"
            ];

        return (this.attributes.state == 'REQUEST-VOL' && this.sent_quote == null) || cantDisRegard.indexOf(this.attributes.state) == -1  ;  
    }

    canSpin()
    {
        let spinStatuses = [
            "NEGOTIATION-VOL"
        ];
        return spinStatuses.indexOf(this.attributes.state) > -1; 
    }

}