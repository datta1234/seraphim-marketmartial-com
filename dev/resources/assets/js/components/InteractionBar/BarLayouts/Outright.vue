<template>
    <b-container fluid dusk="ibar-negotiation-bar-outright">
        
   <!--      <pre style="background: white">
            {{ marketRequest }}
        </pre> -->

        <ibar-user-market-title :title="market_title" :time="market_time" class="mt-1 mb-3"></ibar-user-market-title>
        
        <!-- VOL SPREAD History - Market-->
        <ibar-negotiation-history-market  :message="history_message" :history="marketRequest.quotes" v-if="marketRequest.quotes && !marketRequest.chosen_user_market" class="mb-3"   @update-on-hold="setUpProposal" @update-on-accept="setUpProposal"
 ></ibar-negotiation-history-market>
   
        <!-- Contracts History - Trade-->
        <ibar-negotiation-history-contracts :message="history_message" :history="marketRequest.chosen_user_market.market_negotiations" v-if="marketRequest.chosen_user_market" class="mb-2"></ibar-negotiation-history-contracts>


        <ibar-market-negotiation-contracts class="mb-5" v-if="can_negotiate" @validate-proposal="validateProposal" :check-invalid="check_invalid" :marker-qoute="marker_qoute" :market-negotiation="proposed_user_market_negotiation"></ibar-market-negotiation-contracts>

   

   <b-form-checkbox id="market-request-subscribe" v-model="market_request_subscribe" value="true" unchecked-value="false" v-if="!can_negotiate">
    Alert me when cleared
    </b-form-checkbox>
        
        <b-row class="mb-5">
            <b-col cols="10">
                    <b-col cols="12" v-for="(error,key) in errors" class="text-danger">
                        {{ key }}:{{ error[0] }}
                    </b-col>
                <b-row v-if="removable_conditions.length > 0">
                    <b-col v-for="cond in removable_conditions" class="text-center">
                        <label class="ibar-condition-remove-label" @click="cond.callback">
                            {{ cond.title }}&nbsp;&nbsp;<span class="remove">X</span>
                        </label>
                    </b-col>
                </b-row>
                <b-row class="justify-content-md-center mb-1">
                    <b-col cols="6">

                        <!-- || (marker_qoute && !is_on_hold) && ( !marker_qoute|| (marker_qoute && !marker_qoute.is_repeat)) -->
                       

                        
                         <b-button v-if="marker_qoute || is_on_hold || (marker_qoute && marker_qoute.is_repeat)" class="w-100 mt-1" :disabled="check_invalid || server_loading" size="sm" dusk="ibar-action-amend" variant="primary" @click="amendQoute()">Amend</b-button>


                        <b-button v-if="is_on_hold && (marker_qoute && !marker_qoute.is_repeat)" class="w-100 mt-1" :disabled="server_loading" size="sm" dusk="ibar-action-repeat" variant="primary" @click="repeatQuote()">Repeat</b-button>

                        <b-button v-if="marker_qoute || is_on_hold || (marker_qoute && marker_qoute.is_repeat)" class="w-100 mt-1" :disabled="server_loading" size="sm" dusk="ibar-action-pull" variant="primary" v-b-modal.pullQuote>Pull</b-button>

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
                <b-row class="justify-content-md-center" v-if="!marker_qoute && !marketRequest.chosen_user_market">
                    <b-col cols="6">

                         <b-button v-if="!marker_qoute" class="w-100 mt-1" :disabled="check_invalid || server_loading" size="sm" dusk="ibar-action-send" variant="primary" @click="sendQuote()">Send</b-button>
                        

                        <b-button class="w-100 mt-1" size="sm" dusk="ibar-action-nocares" variant="secondary">No Cares</b-button>
                    </b-col>
                </b-row>

                 <b-row class="justify-content-md-center" v-if="marketRequest.chosen_user_market && can_negotiate">
                    <b-col cols="6">
                         
                         <b-button  class="w-100 mt-1" v-if="marketRequest.is_market_maker && !negotiation_updated" :disabled="server_loading" size="sm" dusk="ibar-action-send" variant="primary" @click="spinNegotiation()">Spin</b-button>


                         <b-button  class="w-100 mt-1" v-if="!marketRequest.is_market_maker || (marketRequest.is_market_maker && negotiation_updated)" :disabled="check_invalid || server_loading" size="sm" dusk="ibar-action-send" variant="primary" @click="sendNegotiation()">Send</b-button>

                        <b-button class="w-100 mt-1" size="sm" dusk="ibar-action-nocares" variant="secondary">No Cares</b-button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <ibar-apply-conditions  v-if="can_negotiate" class="mb-5" :applied-conditions="proposed_user_market_negotiation.conditions" :removable-conditions="removable_conditions"></ibar-apply-conditions>

        <!-- <b-row class="mb-2">
            <b-col>
                <b-form-checkbox v-model="state_premium_calc" value="true" unchecked-value="false"> Apply premium calculator</b-form-checkbox>
            </b-col>
        </b-row> -->
        <ibar-apply-premium-calculator  v-if="can_negotiate" :market-negotiatio="proposed_user_market_negotiation"></ibar-apply-premium-calculator>
        
    </b-container>
</template>
<script>
    import { EventBus } from '../../../lib/EventBus.js';
    import UserMarketRequest from '../../../lib/UserMarketRequest';
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';
    import UserMarket from '../../../lib/UserMarket';
    import moment from 'moment';
    export default {
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
                       this.reset();
                       this.setUpData(); 
                    }
                },
                deep: true
            }
        },
        computed: {
            'marker_qoute': function(){
                return this.marketRequest.quotes.find(quote => quote.is_maker);
            },
            'negotiation_updated': function(){
                    if(this.marketRequest.chosen_user_market && this.last_negotiation != null && (this.last_negotiation.offer_qty != null || this.last_negotiation.bid_qty != null) )
                    {
                      

                        return this.proposed_user_market_negotiation.bid != this.last_negotiation.bid || this.proposed_user_market_negotiation.offer != this.last_negotiation.offer;
                    }else
                    {
                        return false;
                    }

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
            }
        },
        methods: {
            validateProposal:function(check_invalid)
            {
                this.check_invalid = check_invalid;
            },
            updateUserMessages:function()
            {
                //if the users market qoute is placed on hold notify the the current user if it is theres
                if(this.marker_qoute && this.marker_qoute.is_on_hold)
                {
                    this.history_message = "Interest has placed your market on hold. Would you like to improve your spread?";
                }

                if(!this.can_negotiate)
                {
                    this.history_message = "Market is pending. As soon as the market clears, you will be able to participate."; 
                }
            },
            subscribeToMarketRequest(){

            },
            spinNegotiation(){
                
                // link now that we are saving
                this.proposed_user_market.setMarketRequest(this.marketRequest);
                this.user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);

                this.server_loading = true;
                this.proposed_user_market_negotiation.spinNegotiation()   
                .then(response => {

                    this.history_message = response.data.message;
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
                this.user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);

                this.server_loading = true;
                this.proposed_user_market_negotiation.storeNegotiation()
                .then(response => {

                    this.history_message = response.data.message;
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
                this.proposed_user_market.store()
                .then(response => {

                    this.history_message = response.data.message;
                    this.server_loading = false;
                    this.errors = [];
                
               
                })
                .catch(err => {
                    this.server_loading = false;

                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });

            },
            amendQoute() {
               // link now that we are saving
                this.proposed_user_market.setMarketRequest(this.marketRequest);
                this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);

                this.server_loading = true;

                // save
                this.proposed_user_market_negotiation.patchQuote()
                .then(response => {
                    console.log("we just finished amending the quote");
                    this.server_loading = false;                    
                    this.history_message = response.data.message;
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
                    this.history_message = response.data.message;
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
                    this.history_message = response.data.message;
                    
                    this.$refs.pullModal.hide();
                })
                .catch(err => {
                    this.server_loading = false;
                    this.errors = err.errors;
                    this.$refs.pullModal.hide();
                });

            },
            reset() {
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
                    this[k] = defaults[k];
                });

            },
            setUpProposal(){

                // set up the new UserMarket as quote to be sent
                
                /*
                *if there's no chosen user market then work with it under quote
                *else post for the specific user market
                */
                let chosen_user_market =  this.marketRequest.chosen_user_market;
                console.log('the chosen user market',chosen_user_market);

                if(chosen_user_market)
                {
                   this.user_market = this.marketRequest.chosen_user_market;
                }else
                {
                    if(this.marketRequest.sent_quote != null)//already have my qoute
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
                else if(this.marker_qoute != null && (this.marker_qoute.offer_qty != null || this.marker_qoute.bid_qty != null) )
                {
                    console.log("makerQoute",this.marker_qoute);
                    //we are editing our old one
                    this.proposed_user_market_negotiation.id  = this.marker_qoute.id;

                    this.proposed_user_market_negotiation.offer_qty = this.marker_qoute.offer_qty;
                    this.proposed_user_market_negotiation.bid_qty = this.marker_qoute.bid_qty;  
                }else
                {                    
                    this.proposed_user_market_negotiation.offer_qty = this.marketRequest.trade_items.default.Quantity;
                    this.proposed_user_market_negotiation.bid_qty = this.marketRequest.trade_items.default.Quantity;
                }

            },
            setUpData()
            {
                // set up up data
                if(this.marketRequest) {
                    this.market_history = this.user_market ? this.user_market.market_negotiations : this.market_history;
                    
                    this.setUpProposal();
                    this.setDefaultQuantities();
                    this.updateUserMessages();

                    // // relate
                    EventBus.$emit('interactionChange',this.marketRequest);
                }
            },
            init() {
                this.reset();// clear current state
                this.setUpData();
            }
        },
        mounted() {
            this.init();
        }
    }
</script>