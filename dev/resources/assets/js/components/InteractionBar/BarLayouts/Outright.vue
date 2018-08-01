<template>
    <b-container fluid dusk="ibar-negotiation-bar-outright">
        
        <ibar-user-market-title :title="market_title" :time="market_time" class="mt-1 mb-3"></ibar-user-market-title>
        
        <!-- VOL SPREAD History - Market-->
        <ibar-negotiation-history-market :message="history_message" :history="marketRequest.quotes" v-if="marketRequest.quotes" class="mb-3"></ibar-negotiation-history-market>
   
        <!-- Contracts History - Trade-->
        <ibar-negotiation-history-contracts :history="marketRequest.chosen_user_market.negotiations" v-if="marketRequest.chosen_user_market" class="mb-2"></ibar-negotiation-history-contracts>

        <ibar-market-negotiation-contracts class="mb-5" :market-negotiation="proposed_user_market_negotiation"></ibar-market-negotiation-contracts>
        
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
                        <b-button v-if="!marker_qoute" class="w-100 mt-1" :disabled="levels_changed || server_loading" size="sm" dusk="ibar-action-send" variant="primary" @click="sendQuote()">Send</b-button>

                        
                         <b-button v-if=" marker_qoute || is_on_hold || (marker_qoute && marker_qoute.is_repeat)" class="w-100 mt-1" :disabled="levels_changed || server_loading" size="sm" dusk="ibar-action-amend" variant="primary" @click="amendQoute()">Amend</b-button>

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
                <b-row class="justify-content-md-center" v-if="!marker_qoute">
                    <b-col cols="6">
                        <b-button class="w-100" size="sm" dusk="ibar-action-nocares" variant="secondary">No Cares</b-button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <ibar-apply-conditions class="mb-5" :applied-conditions="proposed_user_market_negotiation.conditions" :removable-conditions="removable_conditions"></ibar-apply-conditions>

        <!-- <b-row class="mb-2">
            <b-col>
                <b-form-checkbox v-model="state_premium_calc" value="true" unchecked-value="false"> Apply premium calculator</b-form-checkbox>
            </b-col>
        </b-row> -->
        <ibar-apply-premium-calculator :market-negotiatio="proposed_user_market_negotiation"></ibar-apply-premium-calculator>


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

                user_market: null,
                market_history: [],

                proposed_user_market: new UserMarket(),
                proposed_user_market_negotiation: new UserMarketNegotiation(),

                default_user_market_negotiation:new UserMarketNegotiation(),
                history_message: null,

                removable_conditions: [],

                server_loading: false,
                errors: [],
            };
        },
        watch: {
            'marketRequest': function() {
                this.init();
            }
        },
        computed: {
            'marker_qoute': function(){

                return this.marketRequest.quotes.find(quote => quote.is_maker);
            },
            'is_on_hold': function(){   
                 return  this.marketRequest.quotes.find(quote => quote.is_maker && quote.is_on_hold);

            },
            'levels_changed':function(){
       
                let props = ["bid_qty","bid","offer","offer_qty" ];
                for (var i = 0; i < props.length; i++) {
                    var propName = props[i];

                    // If values of same property are not equal,
                    // objects are not equivalent
                    if (this.proposed_user_market_negotiation[propName] !== this.default_user_market_negotiation[propName])
                    {
                        return false;
                    }
                }
                return true;
            
            },
            'market_title': function() {
                return this.marketRequest.getMarket().title+" "
                +this.marketRequest.trade_items.default[this.$root.config("trade_structure.outright.expiration_date")]+" "
                +this.marketRequest.trade_items.default[this.$root.config("trade_structure.outright.strike")];
            },
            'market_time': function() {
                return this.marketRequest.updated_at.format("HH:mm");
            }
        },
        methods: {
            sendQuote() {

                // link now that we are saving
                this.proposed_user_market.setMarketRequest(this.marketRequest);
                this.server_loading = true;

                // save
                this.proposed_user_market.store()
                .then(response => {

                    this.history_message = response.data.message;
                    this.server_loading = false;
                    //EventBus.$emit('interactionToggle', false);
                })
                .catch(err => {
                    this.server_loading = false;

                    this.errors = err.errors;
                });
            },
            amendQoute() {
               // link now that we are saving
                this.proposed_user_market.setMarketRequest(this.marketRequest);
                this.server_loading = true;

                // save
                this.proposed_user_market_negotiation.patch()
                .then(response => {
                    this.server_loading = false;
                    this.history_message = response.message;
                    this.proposed_user_market_negotiation = response.data.data;
                    
                    this.history_message = response.data.message;
                    this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);
                    //EventBus.$emit('interactionToggle', false);
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
               this.proposed_user_market_negotiation.is_repeat = true;
                this.server_loading = true;

                // save
                this.proposed_user_market_negotiation.patch()
                .then(response => {
                    this.server_loading = false;
                    this.history_message = response.message;
                    this.proposed_user_market_negotiation = response.data.data;
                    
                    this.history_message = response.data.message;
                    this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);
                    this.errors = [];
                    //EventBus.$emit('interactionToggle', false);
                })
                .catch(err => {
                    this.server_loading = false;
                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });
            },
            pullQuote() {
                this.proposed_user_market.setMarketRequest(this.marketRequest);
                this.server_loading = true;
                // save
                this.proposed_user_market.delete()
                .then(response => {
                    this.server_loading = false;
                    this.history_message = response.data.message;
                   // EventBus.$emit('interactionToggle', false);
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

                    removable_conditions: [],
                    history_message: null,
                    errors: [],
                };
                Object.keys(defaults).forEach(k => {
                    this[k] = defaults[k];
                });
            },
            hideModal() {
                this.$refs.pullModal.hide();
            },
            init() {
                this.reset(); // clear current state
                // set up ui data
                if(this.marketRequest) {
                    this.user_market = this.marketRequest.getChosenUserMarket();
                    this.market_history = this.user_market ? this.user_market.market_negotiations : this.market_history;

                   this.proposed_user_market_negotiation = new UserMarketNegotiation();   

                    // set up the new UserMarket as quote to be sent
                    if(this.marker_qoute)//already have my qoute
                    {
                        this.proposed_user_market = this.marker_qoute.getMarketRequest().getUserMarket();
                        //use the id from the usermarket
                        this.proposed_user_market_negotiation.id = this.marker_qoute.getMarketRequest().getUserMarket().getCurrentNegotiation().id;
                    }
                    else
                    {
                        this.proposed_user_market = new UserMarket();
                    }
                    if(this.marker_qoute && this.marker_qoute.is_on_hold)
                    {
                        this.history_message = "Interest has placed your market on hold. Would you like to improve your spread?";
                    }

                    


                    console.log("marker quote =>",this.marker_qoute);
                   

                    
                    // relate
                    this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);

                    //set the quotes here if they already set
                }
            }
        },
        mounted() {
            this.init();
        }
    }
</script>