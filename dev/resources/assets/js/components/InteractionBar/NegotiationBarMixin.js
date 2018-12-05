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
                    console.log("MarketRequest CHANGED! >>> ", newVal, oldVal);
                    if(newVal.id != oldVal.id)
                    {
                        this.init();
                    }else
                    {
                        let vals = {
                            bid: this.proposed_user_market_negotiation.bid != this.proposed_user_market_negotiation._bid_initial_value ? this.proposed_user_market_negotiation.bid : null,
                            offer: this.proposed_user_market_negotiation.offer != this.proposed_user_market_negotiation._offer_initial_value ? this.proposed_user_market_negotiation.offer : null,
                        };
                        this.reset(['history_message']);
                        this.setUpData(); 
                        // ensure values dont change if they werent suposed to
                        if(vals.bid != null) {
                            this.proposed_user_market_negotiation.bid = vals.bid;
                        }
                        if(vals.offer != null) {
                            this.proposed_user_market_negotiation.offer = vals.offer;
                        }
                    }
                },
                deep: true
            },
            'last_negotiation': function(nV) {
                if(this.proposed_user_market_negotiation._offer_initial_value != null && this.proposed_user_market_negotiation.offer == this.proposed_user_market_negotiation._offer_initial_value) {
                    console.log("Setting Offer", nV);
                    // update to new value
                    this.proposed_user_market_negotiation.offer = nV.offer;
                    this.proposed_user_market_negotiation._offer_initial_value = nV.offer;
                }
                if(this.proposed_user_market_negotiation._bid_initial_value != null && this.proposed_user_market_negotiation.bid == this.proposed_user_market_negotiation._bid_initial_value) {
                    console.log("Setting Bid", nV);
                    // update to new value
                    this.proposed_user_market_negotiation.bid = nV.bid;
                    this.proposed_user_market_negotiation._bid_initial_value = nV.bid;
                }
            }
        },
    computed: {
        'negotiation_available': function(){
            // if(this.last_negotiation.is_my_org) {
            //     if(this.cant_amend) {
            //         return this.last_negotiation.isSpun() || this.last_negotiation.isTraded();
            //     }
            // }
            return true;
        },
        'cant_amend': function() {
            if(this.last_negotiation) {
                return !this.last_negotiation.is_my_org 
                    ||  (
                        this.last_negotiation.is_my_org &&
                        ( this.last_negotiation.isSpun() || this.last_negotiation.isTraded() )
                    )
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
            return !this.last_negotiation.hasTimeoutRemaining() || this.last_negotiation.isTraded();
        },
        is_trading_at_best_source: function() {
            return this.marketRequest.chosen_user_market.isTradingAtBest() && this.marketRequest.trading_at_best.is_my_org;
        },
        'can_disregard':function(){
            return this.marketRequest.canApplyNoCares();
        },
        'can_spin':function(){
            return this.marketRequest.canSpin() && this.proposed_user_market_negotiation.id == null;
        },
        'can_click_spin':function(){
            
            if(this.marketRequest.chosen_user_market && this.marketRequest.chosen_user_market.getLastNegotiation())
            {
                let lastNegotiation = this.marketRequest.chosen_user_market.getLastNegotiation();

                return lastNegotiation.bid == this.proposed_user_market_negotiation.bid && 
                lastNegotiation.bid_qty == this.proposed_user_market_negotiation.bid_qty &&
                lastNegotiation.offer == this.proposed_user_market_negotiation.offer &&
                lastNegotiation.offer_qty == this.proposed_user_market_negotiation.offer_qty
            }
            //for cases where thers no chosen user market dont have the button deactivated
            return true;
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
            else if(!this.can_negotiate && !this.is_trading && !this.is_trading_at_best)
            {
                this.history_message = "Market is pending.";
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
            let method = (this.proposed_user_market_negotiation.id != null ? 'amendNegotiation' : 'storeNegotiation' );
            this.proposed_user_market_negotiation[method](this.user_market)
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
            let defaults = {
                state_premium_calc: false,

                user_market: null,
                market_history: [],

                proposed_user_market: new UserMarket(),
                proposed_user_market_negotiation: new UserMarketNegotiation(),
                default_user_market_negotiation: new UserMarketNegotiation(),
                history_message: null,
                removable_conditions: [],
                server_loading: false,
                check_invalid: false,
                errors: [],
            };

            Object.keys(defaults).forEach(k => {
                if(ignore.indexOf(k) == -1) {
                    this.$set(this.$data, k, defaults[k]); 
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
                console.log("Proposal2");
                this.user_market = this.marketRequest.chosen_user_market;
                if(this.last_negotiation) {
                    console.log("Proposal3", this.last_negotiation);
                    if(this.last_negotiation.is_my_org == true) {
                        // its my org... can i ammend?
                        if(!this.cant_amend) {
                            this.proposed_user_market_negotiation.id = this.last_negotiation.id;
                            console.log("Proposal Set", this.proposed_user_market_negotiation.id, this.proposed_user_market_negotiation);
                        }
                    }
                }
            }
            else
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
                if(!this.last_negotiation.isTraded()) {
                    this.proposed_user_market_negotiation.offer_qty = this.last_negotiation.offer_qty;
                    this.proposed_user_market_negotiation.bid_qty = this.last_negotiation.bid_qty;  
                    if(!this.last_negotiation.isSpun()) {

                        // track changes to the values
                        if(!this.proposed_user_market_negotiation._offer_initial_value || this.proposed_user_market_negotiation._offer_initial_value == this.proposed_user_market_negotiation.offer) {
                            // is the same as original
                            this.proposed_user_market_negotiation.offer = this.last_negotiation.offer;
                            this.proposed_user_market_negotiation._offer_initial_value = this.last_negotiation.offer;
                        }

                        if(!this.proposed_user_market_negotiation._bid_initial_value || this.proposed_user_market_negotiation._bid_initial_value == this.proposed_user_market_negotiation.bid) {
                            // is the same as original
                            this.proposed_user_market_negotiation.bid = this.last_negotiation.bid;
                            this.proposed_user_market_negotiation._bid_initial_value = this.last_negotiation.bid;
                        }
                    }
                }

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
            console.log("Setup Started");
            // set up up data
            if(this.marketRequest) {
                console.log("Seting Up:", this.marketRequest);
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
            EventBus.$emit('startReset');
            this.reset();// clear current state
            // clear saved state data on reset
            this.proposed_user_market_negotiation._bid_initial_value = null;
            this.proposed_user_market_negotiation._offer_initial_value = null;
            this.setUpData();
            EventBus.$emit('completeReset');
        }
    }
}