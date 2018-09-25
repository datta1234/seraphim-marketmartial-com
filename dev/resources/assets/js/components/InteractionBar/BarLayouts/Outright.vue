<template>
    <b-container fluid dusk="ibar-negotiation-bar-outright">

        <ibar-user-market-title :title="market_title" :time="market_time" class="mt-1 mb-3"></ibar-user-market-title>
        
        <!-- VOL SPREAD History - Market-->
        <ibar-negotiation-history-market 
         :message="history_message"
         :history="marketRequest.quotes" 
         v-if="marketRequest.quotes && !marketRequest.chosen_user_market" 
         class="mb-3"
         @update-on-hold="setUpProposal" 
         @update-on-accept="setUpProposal"
        >
        </ibar-negotiation-history-market>
   
        <!-- Contracts History - Trade-->
        <ibar-negotiation-history-contracts :message="history_message" :history="marketRequest.chosen_user_market.market_negotiations" v-if="marketRequest.chosen_user_market" class="mb-2"></ibar-negotiation-history-contracts>


        <ibar-market-negotiation-contracts class="mb-5" v-if="can_negotiate" @validate-proposal="validateProposal" :disabled="fok_active" :check-invalid="check_invalid" :current-negotiation="last_negotiation" :market-negotiation="proposed_user_market_negotiation"></ibar-market-negotiation-contracts>

   

        <b-form-checkbox id="market-request-subscribe" v-model="market_request_subscribe" value="true" unchecked-value="false" v-if="!can_negotiate">
            Alert me when cleared
        </b-form-checkbox>
        
        <b-row class="mb-5">
            <b-col cols="10">
                <b-col cols="12" v-for="(error,key) in errors" :key="key" class="text-danger">
                    {{ error[0] }}
                </b-col>
                <ibar-remove-conditions  v-if="can_negotiate" :market-negotiation="proposed_user_market_negotiation"></ibar-remove-conditions>
                <b-row class="justify-content-md-center mb-1">
                    <b-col cols="6">

                        <!-- || (maker_quote && !is_on_hold) && ( !maker_quote|| (maker_quote && !maker_quote.is_repeat)) -->
                       

                        
                         <b-button v-if="maker_quote || is_on_hold || (maker_quote && maker_quote.is_repeat)" class="w-100 mt-1" :disabled="check_invalid || server_loading" size="sm" dusk="ibar-action-amend" variant="primary" @click="amendQuote()">Amend</b-button>


                        <b-button v-if="is_on_hold && (maker_quote && !maker_quote.is_repeat)" class="w-100 mt-1" :disabled="server_loading" size="sm" dusk="ibar-action-repeat" variant="primary" @click="repeatQuote()">Repeat</b-button>

                        <b-button v-if="maker_quote || is_on_hold || (maker_quote && maker_quote.is_repeat)" class="w-100 mt-1" :disabled="server_loading" size="sm" dusk="ibar-action-pull" variant="primary" v-b-modal.pullQuote>Pull</b-button>

                        <!-- Modal Component -->
                        <b-modal ref="pullModal" id="pullQuote" title="Pull Market" class="mm-modal mx-auto">
                            <p>Are you sure you want to pull this quote?</p>
                            <div slot="modal-footer" class="w-100">
                                <b-row align-v="center">
                                    <b-col cols="12">
                                        <b-button class="mm-modal-button mr-2 w-25" @click="pullQuote()">Pull</b-button>
                                        <b-button class="btn mm-modal-button ml-2 w-25 btn-secondary" @click="hideModal()">Cancel</b-button>
                                    </b-col>
                                </b-row>
                            </div>
                        </b-modal>


                    </b-col>
                </b-row>
                <b-row class="justify-content-md-center" v-if="!maker_quote && !marketRequest.chosen_user_market">
                    <b-col cols="6">

                        <b-button class="w-100 mt-1"
                          v-if="!maker_quote" 
                          :disabled="check_invalid || server_loading || fok_active" 
                          size="sm" 
                          dusk="ibar-action-send" 
                          variant="primary" 
                          @click="sendQuote()">
                            Send
                        </b-button>
                        

                    </b-col>
                </b-row>

                 <b-row class="justify-content-md-center" v-if="marketRequest.chosen_user_market && can_negotiate">
                    <b-col cols="6">
                         
                        <b-button  class="w-100 mt-1" 
                         :disabled="check_invalid || server_loading || fok_active" 
                         size="sm" 
                         dusk="ibar-action-send" 
                         variant="primary" 
                         @click="sendNegotiation()">
                                Send
                        </b-button>
                        <b-button class="w-100 mt-1" 
                         :disabled="fok_active" 
                         v-if="can_spin" 
                         size="sm" 
                         dusk="ibar-action-send" 
                         variant="primary" 
                         @click="spinNegotiation()">
                            Spin
                        </b-button>
                    </b-col>
                </b-row>
                <b-row class="justify-content-md-center">
                    <b-col cols="6">
                        <!-- !maker_quote && !marketRequest.chosen_user_market -->
                         <b-button  v-if="can_disregard && !in_no_cares" class="w-100 mt-1" size="sm" dusk="ibar-action-nocares" variant="secondary" @click="addToNoCares()">No Cares</b-button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <ibar-fok-active :market-negotiation="marketRequest.chosen_user_market.active_fok" v-if="fok_active"></ibar-fok-active>
            
        <ibar-apply-conditions v-if="can_negotiate && !fok_active" class="mb-5" :market-negotiation="proposed_user_market_negotiation"></ibar-apply-conditions>

        <!-- <b-row class="mb-2">
            <b-col>
                <b-form-checkbox v-model="state_premium_calc" value="true" unchecked-value="false"> Apply premium calculator</b-form-checkbox>
            </b-col>
        </b-row> -->

      <!--   <ibar-apply-premium-calculator  v-if="can_negotiate" :market-negotiatio="proposed_user_market_negotiation"></ibar-apply-premium-calculator> -->


    </b-container>
</template>
<script>
    import { EventBus } from '~/lib/EventBus.js';
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import UserMarket from '~/lib/UserMarket';
    
    import moment from 'moment';
    
    import IbarApplyConditions from '../MarketComponents/ApplyConditionsComponent';
    import IbarRemoveConditions from '../MarketComponents/RemoveConditionsComponent';
    import IbarFoKActive from '../MarketComponents/FoKActiveComponent';

    const showMessagesIn = [
        "market_request_store",
        "market_request_update",
        "market_request_delete",
        "market_negotiation_store"
    ];


    export default {
        components: {
            IbarApplyConditions,
            IbarRemoveConditions,
            'ibar-fok-active': IbarFoKActive,
        },
        props: {
            marketRequest: {
                type: UserMarketRequest
            }
        },
        data() {
            return {
                state_premium_calc: false,
                market_request_subscribe: false,
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
            'fok_active': function() {
                return (this.marketRequest.chosen_user_market !== null && this.marketRequest.chosen_user_market.active_fok !== null );
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
            'market_title': function() {
                return this.marketRequest.getMarket().title+" "
                +this.marketRequest.trade_items.default[this.$root.config("trade_structure.outright.expiration_date")]+" "
                +this.marketRequest.trade_items.default[this.$root.config("trade_structure.outright.strike")];
            },
            'market_time': function() {
                return this.marketRequest.updated_at.format("HH:mm");
            },
            'can_negotiate':function(){
                return this.marketRequest.canNegotiate();
            },
            'can_disregard':function(){
                return this.marketRequest.canApplyNoCares();
            },
            'can_spin':function(){
                return this.marketRequest.canSpin();
            },
            'in_no_cares':function(){
                return this.$root.no_cares.indexOf(this.marketRequest.id) > -1;
            }
        },
        methods: {
            validateProposal:function(check_invalid)
            {
                this.check_invalid = check_invalid;
            },
            updateMessage: function(messageData)
            {
                if(messageData.user_market_request_id == this.marketRequest.id)
                {
                    let message = messageData.message;
                    if(message !== null && typeof message === "object" && showMessagesIn.indexOf(message.key) > -1)
                    {
                       this.history_message = message.data;
                    }else if(message === null)
                    {
                        this.history_message = null;
                    } 
                }
               
            },
            calcUserMessages:function()
            {
                //if the users market quote is placed on hold notify the the current user if it is theres
                if(this.maker_quote && this.maker_quote.is_on_hold)
                {
                    this.history_message = "Interest has placed your market on hold. Would you like to improve your spread?";
                }else if(!this.can_negotiate)
                {
                    this.history_message = "Market is pending. As soon as the market clears, you will be able to participate."; 
                }
            },
            subscribeToMarketRequest(){

            },
            spinNegotiation(){
                
                this.server_loading = true;
                this.proposed_user_market_negotiation.spinNegotiation(this.user_market)   
                .then(response => {
                    this.server_loading = false;
                    this.errors = [];
                })
                .catch(err => {
                    this.server_loading = false;

                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });

            },
            sendNegotiation(){

                // link now that we are saving
                this.proposed_user_market.setMarketRequest(this.marketRequest);
                // this.user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);
                console.log("this is the proposed user market",this.user_market);
                this.server_loading = true;
                this.proposed_user_market_negotiation.storeNegotiation(this.user_market)
                .then(response => {
                    this.server_loading = false;
                    this.errors = [];
                })
                .catch(err => {
                    this.server_loading = false;

                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
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
                    console.log("this is an error",err);

                    this.server_loading = false;
                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });

            },
            amendQuote() {

                this.server_loading = true;

                // save
                this.proposed_user_market_negotiation.patchQuote(this.marketRequest, this.proposed_user_market)
                .then(response => {

                    this.server_loading = false;                    
                    this.errors = [];
                    
                })
                .catch(err => {
                    this.server_loading = false;
                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
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
                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });
            },
            pullQuote() {
                
                this.proposed_user_market.setMarketRequest(this.marketRequest);
                this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);

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
                        this.proposed_user_market = this.marketRequest.sent_quote;
                    }
                    else
                    {
                        this.proposed_user_market = new UserMarket();
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
                    this.proposed_user_market_negotiation.offer_qty = this.marketRequest.trade_items.default.Quantity;
                    this.proposed_user_market_negotiation.bid_qty = this.marketRequest.trade_items.default.Quantity;
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
        },
        mounted() {
            this.init();
            EventBus.$on('notifyUser',this.updateMessage);

        }
    }
</script>