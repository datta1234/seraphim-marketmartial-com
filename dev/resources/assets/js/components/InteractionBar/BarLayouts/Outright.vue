<template>
    <b-container fluid dusk="ibar-negotiation-bar-outright">
        
        <ibar-user-market-title :title="market_title" :time="market_time" class="mt-1 mb-3"></ibar-user-market-title>
        
        <ibar-negotiation-history-contracts :history="market_history" class="mb-2"></ibar-negotiation-history-contracts>

        <ibar-market-negotiation-contracts class="mb-5" :market-negotiation="proposed_user_market_negotiation"></ibar-market-negotiation-contracts>
        

        <b-row class="mb-5">
            <b-col>
                <b-row class="justify-content-md-center mb-1">
                    <b-col>
                        <b-button class="w-100" size="sm" variant="primary" @click="sendQuote()">Send</b-button>
                    </b-col>
                </b-row>
                <b-row class="justify-content-md-center">
                    <b-col>
                        <b-button class="w-100" size="sm" variant="secondary">No Cares</b-button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>

        <ibar-apply-conditions class="mb-5" :conditions="proposed_user_market_negotiation.conditions"></ibar-apply-conditions>

        <b-row class="mb-2">
            <b-col>
                <b-form-checkbox v-model="state_premium_calc" value="true" unchecked-value="false"> Apply premium calculator</b-form-checkbox>
            </b-col>
        </b-row>

    </b-container>
</template>
<script>
    import { EventBus } from '../../../lib/EventBus.js';
    import UserMarketRequest from '../../../lib/UserMarketRequest';
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';
    import UserMarket from '../../../lib/UserMarket';

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
                market_time: "",

                proposed_user_market: new UserMarket(),
                proposed_user_market_negotiation: new UserMarketNegotiation(),
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
            }
        },
        methods: {
            sendQuote() {
                console.log(this.proposed_user_market);
            },
            setMarketTitle() {
                
            },
            setMarketTime() {
                this.market_time = "10:10";
            },
            reset() {
                let defaults = {
                    state_premium_calc: false,

                    user_market: null,
                    market_history: [],
                    market_time: ""
                };
                Object.keys(defaults).forEach(k => {
                    this[k] = defaults[k];
                });
            },
            init() {
                console.log("Mounted BAR", this.marketRequest);
                this.reset(); // clear current state

                // set up ui data
                if(this.marketRequest) {
                    this.user_market = this.marketRequest.getChosenUserMarket();
                    this.market_history = this.user_market ? this.user_market.market_negotiations : this.market_history;
                    this.setMarketTitle();
                    this.setMarketTime();

                    // set up the new UserMarket as quote to be sent
                    this.proposed_user_market = new UserMarket();
                    this.proposed_user_market_negotiation = new UserMarketNegotiation();
                    
                    // relate
                    this.proposed_user_market.setCurrentNegotiation(this.proposed_user_market_negotiation);
                }

            }
        },
        mounted() {
            this.init();
        }
    }
</script>