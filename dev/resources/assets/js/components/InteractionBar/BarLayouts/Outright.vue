<template>
    <b-container fluid dusk="ibar-negotiation-bar-outright">
        
        <ibar-user-market-title :title="market_title" :time="market_time" class="mt-1 mb-3"></ibar-user-market-title>
        
        <!-- VOL SPREAD History - Market-->
        <ibar-negotiation-history-market :history="marketRequest.quotes" v-if="marketRequest.quotes" class="mb-3"></ibar-negotiation-history-market>

        <!-- Contracts History - Trade-->
        <ibar-negotiation-history-contracts :history="marketRequest.chosen_user_market.negotiations" v-if="marketRequest.chosen_user_market" class="mb-2"></ibar-negotiation-history-contracts>

        <ibar-market-negotiation-contracts class="mb-5" :market-negotiation="proposed_user_market_negotiation"></ibar-market-negotiation-contracts>

        

        <b-row class="mb-5">
            <b-col cols="10">
                <b-row v-if="errors.length > 1">
                    <b-col cols="12" v-for="error in errors" class="danger">
                        {{ error }}
                    </b-col>
                </b-row>
                <b-row v-if="removable_conditions.length > 0">
                    <b-col v-for="cond in removable_conditions" class="text-center">
                        <label class="ibar-condition-remove-label" @click="cond.callback">
                            {{ cond.title }}&nbsp;&nbsp;<span class="remove">X</span>
                        </label>
                    </b-col>
                </b-row>
                <b-row class="justify-content-md-center mb-1">
                    <b-col cols="6">
                        <b-button class="w-100" size="sm" dusk="ibar-action-send" variant="primary" @click="sendQuote()">Send</b-button>
                    </b-col>
                </b-row>
                <b-row class="justify-content-md-center">
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

                removable_conditions: [],

                errors: [],
            };
        },
        watch: {
            'marketRequest': function() {
                this.init();
            }
        },
        computed: {
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
                this.proposed_user_market.store()
                .then(response => {
                    console.log("Got It: ", response);
                    EventBus.$emit('interactionToggle', false);
                })
                .catch(err => {
                    this.errors = err.errors;
                });
            },
            reset() {
                let defaults = {
                    state_premium_calc: false,

                    user_market: null,
                    market_history: [],
                };
                Object.keys(defaults).forEach(k => {
                    this[k] = defaults[k];
                });
            },
            init() {
                this.reset(); // clear current state

                // set up ui data
                if(this.marketRequest) {
                    this.user_market = this.marketRequest.getChosenUserMarket();
                    this.market_history = this.user_market ? this.user_market.market_negotiations : this.market_history;

                    // set up the new UserMarket as quote to be sent
                    this.proposed_user_market = new UserMarket();
                    this.proposed_user_market_negotiation = new UserMarketNegotiation();
                    
                    // relate
                    this.proposed_user_market.setMarketRequest(this.marketRequest);
                    this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);
                }

            }
        },
        mounted() {
            this.init();
        }
    }
</script>