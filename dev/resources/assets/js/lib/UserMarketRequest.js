import BaseModel from './BaseModel';
import UserMarket from './UserMarket';
import UserMarketQuote from './UserMarketQuote';
import Errors from './Errors';
import Config from './Config';
import util from './util';

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
        const defaults = {
            id: "",
            trade_structure: "",
            market_id:"",
            is_interest: false,
            is_market_maker: false,
            attributes: {
                state: "",
                bid_state: "",
                offer_state: "",
                action_needed: false,
                is_interest: false
            },
            quote: null,
            ratio: null,
            chosen_user_market: null,
            created_at: moment(),
            updated_at: moment(),
            default_quantity: 500,

            // optional
            user: null,
            org: null,
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

       // register user_market
        if(options && options.chosen_user_market) {
            this.setChosenUserMarket(options.chosen_user_market);
        }

         // register user_market
        if(options && options.user_market) {
            this.setUserMarket(options.user_market);
        }
        
        this.stage(); // refresh state 
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
        this.stage(); // refresh state
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
    *   getter for sent_quote - Set the UserMarketRequest setSentQuote
    *   @param {UserMarket} user_market - UserMarket object
    */
    get sent_quote() {
        return this.quotes.find(q => {
            return q.is_maker;
        });
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
    // @TODO - I dont know why this is even here, Removed because it was overriding the original method.
    //          Does not seem to be used anywhere in the js.
    /*getChosenUserMarket() {
        return this.user_market;
    }*/

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

    get market() {
        return this._market;
    }

    /**
    *   actionTaken - Alerts the server that an action has been taken on a Market Request
    *   @return response from the request or the error
    */
    actionTaken() {
        if(!this.attributes.action_needed) {
            return new Promise((resolve, reject) => {
                reject(new Errors("No actions needed"));
            });
        }
        return new Promise((resolve, reject) => {
            axios.post(axios.defaults.baseUrl + '/trade/user-market-request/'+ this.id +'/action-taken', 
                {
                    'action_needed': false
                },
                {
                    headers: {
                        [Config.get('app.ajax.headers.ignore')]: true
                    }
                }
            )
            .then(response => {
                this.attributes.action_needed = response.data.data.action_needed;
                resolve(response);
            })
            .catch(err => {
                reject(err);
            }); 
        });
    }

    /**
    *   deactivate the market request (admin only)
    */
    deactivate()
    {
        return axios.delete(axios.defaults.baseUrl + '/trade/market/' + this.market_id + '/market-request/'+this.id)
            .then(response => {
                return response;
            });
    }

    /**
    *   canNegotiate - Checks to see if the user can take any negotiation on the current ste of the request
    *   @return {Boolean}
    */
    canNegotiate()
    {
        let tradebleStatuses = [
            "REQUEST-SENT",
            "REQUEST",
            "REQUEST-SENT-VOL",
            "REQUEST-VOL",
            "NEGOTIATION-VOL",
            "NEGOTIATION-OPEN-VOL",
            "TRADE-NEGOTIATION-OPEN",
            // Adding these broke things [MM-820]
            // "TRADE-NEGOTIATION-SENDER",
            // "TRADE-NEGOTIATION-COUNTER",
        ];
            
        return  tradebleStatuses.indexOf(this.attributes.state) > -1;
    }

    /**
    *   canInitiateTrade - Checks to see if the user can start a trade on any negotiation on the current state of the request
    *   @return {Boolean}
    */
    canInitiateTrade()
    {
        let tradebleStatuses = [
            "NEGOTIATION-VOL",
            "NEGOTIATION-OPEN-VOL",
        ];

        return tradebleStatuses.indexOf(this.attributes.state) > -1;
    }

    /**
    *   isRequestPhase - Checks to see if the user is in any request phase on the current state of the request
    *   
    *   @return {Boolean} - if the request has any of the listed statuses
    */
    isRequestPhase()
    {
        let tradebleStatuses = [
            "REQUEST-SENT",
            "REQUEST",
            "REQUEST-SENT-VOL",
            "REQUEST-VOL",
        ];
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

    isTrading()
    {
        let tradingStates = [
            "TRADE-NEGOTIATION-SENDER",
            "TRADE-NEGOTIATION-COUNTER",
            "TRADE-NEGOTIATION-PENDING",
            "TRADE-NEGOTIATION-BALANCER"
        ];

        console.log("is trading got called",tradingStates.indexOf(this.attributes.state) > -1,this.is_trading_at_best == false,this.is_trading_at_best,this.is_trading_at_best_closed);

        return tradingStates.indexOf(this.attributes.state) > -1 
            && (this.is_trading_at_best == false || this.is_trading_at_best_closed); // if its trading at best, then its not trading
    }

    isInvolvedInTrade()
    {
        let tradingStates = [
            "TRADE-NEGOTIATION-SENDER",
            "TRADE-NEGOTIATION-COUNTER",
            "TRADE-NEGOTIATION-BALANCER"
        ];

        return  tradingStates.indexOf(this.attributes.state) > -1;
    }

    get is_trading_at_best() {
        // console.log("is_trading_at_best ",this.chosen_user_market != null && this.chosen_user_market.trading_at_best != null);
        return (this.chosen_user_market != null && this.chosen_user_market.trading_at_best != null);
    }

    get is_trading_at_best_closed() {
        // console.log("is_trading_at_best_closed()",(this.chosen_user_market != null && this.chosen_user_market.trading_at_best != null && !this.chosen_user_market.trading_at_best.hasTimeoutRemaining()));
        return (this.chosen_user_market !=null && this.chosen_user_market.trading_at_best != null && !this.chosen_user_market.trading_at_best.hasTimeoutRemaining());
    }

    canCounterTrade()
    {
        let canCounterTrade = [
            "TRADE-NEGOTIATION-COUNTER"
        ];
        return canCounterTrade.indexOf(this.attributes.state) > -1; 
    }

    mustWorkBalance()
    {
        let mustWorkBalance = [
            "TRADE-NEGOTIATION-BALANCER"
        ];
        return mustWorkBalance.indexOf(this.attributes.state) > -1;
    }

    stage() 
    {
        if(this.chosen_user_market == null) {
            this._stage = "quote";
            return this._stage;
        }
        this._stage = "market";
        return this._stage;
    }

    get trade_structure_slug() {
        return util.resolveTradeStructureSlug(this.trade_structure);
    }

    defaultQuantity()
    {
        /*let group = Object.values(this.trade_items).find(item => item.choice == false);
        let ts = this.trade_structure_slug;
        let conf = Config.get('trade_structure.'+this.trade_structure_slug+'.quantity');
        let val = group[conf];
        return val;*/

        return this.default_quantity;
    }

    getQuantityType()
    {        
       if(this.getMarket())
        {
            return this.getMarket().title == "SINGLES" ? "Rm" : "Contracts";
        }
    }

    myOrgInvolved() {
        if(this.chosen_user_market) {
            // NEGOTIATIONS
            let negotiation = this.chosen_user_market.getLastNegotiation();
            // is on one of the sides
            return negotiation && negotiation.level_sides.length != 0;
        }
        // QUOTES
        // I have sent a quote
        return this.quotes.length > 0 && this.quotes.findIndex(q => q.is_maker) != -1;
    }
}