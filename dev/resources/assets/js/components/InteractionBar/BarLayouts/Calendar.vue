<template>
    <b-container fluid dusk="ibar-negotiation-bar-calendar">
        
        <ibar-user-market-title :title="market_title" :time="market_time" class="mt-1 mb-3"></ibar-user-market-title>
        
        <ibar-negotiation-history-contracts :history="market_history" class="mb-2"></ibar-negotiation-history-contracts>

        <ibar-market-negotiation-contracts class="mb-5" :market-negotiation="proposed_user_market_negotiation"></ibar-market-negotiation-contracts>

        <!-- <ibar-apply-conditions class="mb-5" :conditions=""></ibar-apply-conditions> -->

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
    export default {
        props: {
            marketRequest: {
                type: UserMarketRequest
            }
        },
        data() {
            return {
                bid: null,
                offer: null,
                bid_qty: 0,
                offer_qty: 0,

                state_conditions: false,
                state_premium_calc: false,

                user_market: null,
                market_history: [],
                market_time: ""
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
                +this.marketRequest.trade_items.default["Expiration Date"]+" "
                +this.marketRequest.trade_items.default["Strike"];
            }
        },
        methods: {
            setMarketTitle() {
                
            },
            setMarketTime() {
                this.market_time = "10:10";
            },
            reset() {
                let defaults = {
                    bid: null,
                    offer: null,
                    bid_qty: 0,
                    offer_qty: 0,

                    state_conditions: false,
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
                this.reset();
                if(this.marketRequest) {
                    this.user_market = this.marketRequest.getChosenUserMarket();
                    this.market_history = this.user_market ? this.user_market.market_negotiations : this.market_history;
                    this.setMarketTitle();
                    this.setMarketTime();
                }
            }
        },
        mounted() {
            this.init();
        }
    }
</script>