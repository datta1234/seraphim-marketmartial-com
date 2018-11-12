import { EventBus } from '~/lib/EventBus.js';
import UserMarketRequest from '~/lib/UserMarketRequest';
import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
import UserMarket from '~/lib/UserMarket';

import moment from 'moment';

export default {
    props: {
        marketRequest: {
            type: UserMarketRequest
        }
    },
    watch: {
            'marketRequest': {
                handler: function(newVal, oldVal) {
                    
                    if(newVal.id != oldVal.id)
                    {
                        this.init();
                    }else
                    {
                      this.reset(['history_message']);
                      this.setUpData(); 
                    }
                },
                deep: true
            }
        },
    computed: {
        'last_is_self': function() {
            if(this.last_negotiation) {
                return this.last_negotiation.is_my_org;
            }
            return false;
        },
        'meet_in_the_middle_proposed': function(){
            return this.proposed_user_market_negotiation.cond_buy_mid != null;
        },
        'maker_quote': function() {
            return this.marketRequest.quotes.find(quote => quote.is_maker); // ??? why not chosen user market ???
        },
        'last_negotiation': function (){
           let chosen_market = this.marketRequest.chosen_user_market;
            if(chosen_market && chosen_market.market_negotiations.length >0)
            {
                return chosen_market.market_negotiations[chosen_market.market_negotiations.length - 1];
            }

            return null;
        },
        'is_on_hold': function(){   
             return  this.marketRequest.quotes.find(quote => quote.is_maker && quote.is_on_hold);
        },
        // Should be Overridden in component
        'market_title': function() {
            return this.marketRequest.trade_items.default.tradable.title;
        },
        'market_time': function() {
            return this.marketRequest.updated_at.format("HH:mm");
        },
        'can_negotiate':function(){
            return this.marketRequest.canNegotiate();
        },
        is_trading: function(){
            return this.marketRequest.isTrading();
        },
        is_trading_at_best: function() {
            return this.marketRequest.chosen_user_market.isTradingAtBest();
        },
        is_trading_at_best_closed: function() {
            return !this.last_negotiation.hasTimeoutRemaining();
        },
        'can_disregard':function(){
            return this.marketRequest.canApplyNoCares();
        },
        'can_spin':function(){
            return this.marketRequest.canSpin();
        },
        'in_no_cares':function(){
            return this.$root.no_cares.indexOf(this.marketRequest.id) > -1;
        },
        'mustWorkBalance':function(){
            return this.marketRequest.mustWorkBalance();
        }
    },
    methods: {
        conditionActive(type) {
            if( this.marketRequest.chosen_user_market !== null && 
                this.marketRequest.chosen_user_market.active_conditions !== null 
            ) {
                for(let i = 0, cond; cond = this.marketRequest.chosen_user_market.active_conditions[i]; i++) {
                    if(cond.type == type) {
                        return true;
                    }
                }
            }
            return false;
        },
        validateProposal:function(check_invalid) {
            this.check_invalid = check_invalid;
        },
        updateMessage: function(messageData) {
            console.log("The message and the data",messageData);
            this.history_message = null;

            if(messageData.user_market_request_id == this.marketRequest.id)
            {
                let message = messageData.message;
                if(message !== null && typeof message === "object" && this.showMessagesIn.indexOf(message.key) > -1)
                {
                   this.history_message = message.data;
                }else if(message === null)
                {
                    this.history_message = null;
                } 
            }
           
        },
        calcUserMessages:function() {
            //if the users market quote is placed on hold notify the the current user if it is theres
            if(this.maker_quote && this.maker_quote.is_on_hold)
            {
                this.history_message = "Interest has placed your market on hold. Would you like to improve your spread?";
            }
            else 
            if(!this.can_negotiate && !this.is_trading && !this.is_trading_at_best)
            {
                this.history_message = "Market is pending. As soon as the market clears, you will be able to participate."; 
            }
        },
        subscribeToMarketRequest() {

        },
        spinNegotiation() {
            
            this.server_loading = true;
            this.proposed_user_market_negotiation.spinNegotiation(this.user_market)   
            .then(response => {
                this.server_loading = false;
                this.errors = [];
            })
            .catch(err => {
                this.server_loading = false;

                this.history_message = err.message;
                this.errors = err.errors;
            });

        },
        sendNegotiation(){

            // link now that we are saving
            this.proposed_user_market.setMarketRequest(this.marketRequest);
            // this.user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);
            this.server_loading = true;
            this.proposed_user_market_negotiation.storeNegotiation(this.user_market)
            .then(response => {
                this.server_loading = false;
                this.errors = [];
            })
            .catch(err => {
                this.server_loading = false;

                this.history_message = err.message;
                this.errors = err.errors;
            });


        },
        improveBestNegotiation() {
            this.server_loading = true;
            this.marketRequest.chosen_user_market.trading_at_best.improveBestNegotiation(this.proposed_user_market_negotiation)
            .then(response => {
                this.server_loading = false;
                this.errors = [];
            })
            .catch(err => {
                this.server_loading = false;

                this.history_message = err.message;
                this.errors = err.errors;
            });
        },
        sendQuote() {

            // link now that we are saving

            this.proposed_user_market.setMarketRequest(this.marketRequest);
            this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);

            this.server_loading = true;

            // save
            this.proposed_user_market.store(this.marketRequest)
            .then(response => {

                this.server_loading = false;
                this.errors = [];
            
           
            })
            .catch(err => {
                this.server_loading = false;
                this.history_message = err.message;
                this.errors = err.errors;
            });

        },
        amendQuote() {

            this.server_loading = true;

            // save
            this.proposed_user_market_negotiation.patchQuote(this.marketRequest, this.proposed_user_market)
            .then(response => {

                this.proposed_user_market = new UserMarket();
                this.server_loading = false;                    
                this.errors = [];
                
            })
            .catch(err => {
                this.server_loading = false;
                this.history_message = err.message;
                this.errors = err.errors;
            });

        },
        repeatQuote() {
           this.proposed_user_market.setMarketRequest(this.marketRequest);
           this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);              
           this.server_loading = true;
            // save
            this.proposed_user_market_negotiation.repeatQuote()
            .then(response => {
                this.server_loading = false;
                this.errors = [];                   
            })
            .catch(err => {
                this.server_loading = false;
                this.history_message = err.message;
                this.errors = err.errors;
            });
        },
        pullQuote() {
            
            this.proposed_user_market.setMarketRequest(this.marketRequest);
            // this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);
            console.log(this.proposed_user_market);

            this.server_loading = true;
            // save
            this.proposed_user_market.delete()
            .then(response => {
                this.server_loading = false;                    
                this.$refs.pullModal.hide();
            })
            .catch(err => {
                this.server_loading = false;
                this.errors = err.errors;
                this.$refs.pullModal.hide();
            });

        },
        reset(ignore = []) {
            console.log("i got called");
            let defaults = {
                state_premium_calc: false,

                user_market: null,
                market_history: [],

                proposed_user_market: new UserMarket(),
                proposed_user_market_negotiation: new UserMarketNegotiation(),
                default_user_market_negotiation:new UserMarketNegotiation(),
                history_message: null,
                removable_conditions: [],
                server_loading: false,
                check_invalid: false,
                errors: [],
            };

            Object.keys(defaults).forEach(k => {
                if(ignore.indexOf(k) == -1)
                {
                   this[k] = defaults[k]; 
                }
            });
            
            this.$forceUpdate();
        },
        setUpProposal(){

            // set up the new UserMarket as quote to be sent
            
            /*
            *if there's no chosen user market then work with it under quote
            *else post for the specific user market
            */
            let chosen_user_market =  this.marketRequest.chosen_user_market;
            if(chosen_user_market)
            {
               this.user_market = this.marketRequest.chosen_user_market;
            }else
            {
                if(this.marketRequest.sent_quote != null)//already have my quote
                {
                    this.proposed_user_market.id = this.marketRequest.sent_quote.id;
                }

            }

        },
        hideModal() {
            this.$refs.pullModal.hide();
        },
        setDefaultQuantities() {
            //if we have a chosen negotiation use that else its still dealing with quotes
            if(this.marketRequest.chosen_user_market && this.last_negotiation != null && (this.last_negotiation.offer_qty != null || this.last_negotiation.bid_qty != null) )
            {
                this.proposed_user_market_negotiation.offer_qty = this.last_negotiation.offer_qty;
                this.proposed_user_market_negotiation.bid_qty = this.last_negotiation.bid_qty;  

                this.proposed_user_market_negotiation.offer = this.last_negotiation.offer;
                this.proposed_user_market_negotiation.bid = this.last_negotiation.bid; 

            }
            else if(this.maker_quote != null && (this.maker_quote.offer_qty != null || this.maker_quote.bid_qty != null) )
            {
                //we are editing our old one
                this.proposed_user_market_negotiation.id  = this.maker_quote.id;

                this.proposed_user_market_negotiation.offer_qty = this.maker_quote.offer_qty;
                this.proposed_user_market_negotiation.bid_qty = this.maker_quote.bid_qty;  
            }
            else
            {
                this.proposed_user_market_negotiation.offer_qty = this.marketRequest.defaultQuantity();
                this.proposed_user_market_negotiation.bid_qty = this.marketRequest.defaultQuantity();
            }
        },
        setUpData() {
            // set up up data
            if(this.marketRequest) {
                console.log(this.marketRequest);
                this.market_history = this.user_market ? this.user_market.market_negotiations : this.market_history;
                
                this.setUpProposal();
                this.setDefaultQuantities();
                this.calcUserMessages();

                // // relate
                EventBus.$emit('interactionChange',this.marketRequest);
                EventBus.$emit('removefromNoCares',this.marketRequest.id);

            }
        },
        addToNoCares() {
            EventBus.$emit('addToNoCares',this.marketRequest.id);
        },
        init() {
            this.reset();// clear current state
            this.setUpData();
        }
    }
}