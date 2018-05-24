<template>
    <b-container fluid>
        
        <ibar-user-market-title :title="marketTitle" :time="marketTime" class="mb-3"></ibar-user-market-title>
        
        <ibar-negotiation-history :history="market_history" class="mb-2"></ibar-negotiation-history>

        <ibar-market-negotiation class="mb-5"></ibar-market-negotiation>

        <b-row class="mb-5">
            <b-col>
                <b-form-checkbox v-model="state_conditions" value="true" unchecked-value="false"> Apply a condition</b-form-checkbox>
            </b-col>
        </b-row>

        <b-row class="mb-2">
            <b-col>
                <b-form-checkbox v-model="state_premium_calc" value="true" unchecked-value="false"> Apply premium calculator</b-form-checkbox>
            </b-col>
        </b-row>

    </b-container>
</template>

<script>
    const UserMarketRequest = require('../../lib/UserMarketRequest');
    const MarketNegotiation = require('../../lib/MarketNegotiation');
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
            };
        },
        computed: {
            marketTitle(){
                return  this.marketRequest.getParent().title+" "
                    +   this.marketRequest.attributes.expiration_date.format("MMM D")+" "
                    +   this.marketRequest.attributes.strike;
            },
            marketTime(){
                return "10:10";
            }
        },
        methods: {
            
        },
        mounted() {
            this.user_market = this.marketRequest.getChosenUserMarket();
            this.market_history = this.user_market ? this.user_market.market_negotiations : this.market_history;
        }
    }
</script>