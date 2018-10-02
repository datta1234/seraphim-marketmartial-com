<template>
    <b-row dusk="ibar-mim-active" class="active-cond-bar">
        <b-col cols="12">
            <div class="text-center">
                <strong>Meet in the Middle: {{ type }}</strong>
            </div>
        </b-col>
        <b-col cols="12" class="mb-1">
            <div class="cond-bar">
                <b-row id="cond-container" class="trade-popover" no-gutters>
                    <b-col cols="6">
                        <span>
                            Trade At {{ value }}
                        </span>
                    </b-col>
                    <b-col cols="2">
                        <span id="meet-in-middle-do-trade">
                            <a href="" @click.prevent.stop="doTrade">{{ type }}</a>
                        </span>
                    </b-col>
                    <b-col cols="4">
                        <span>
                            <a href="" @click.prevent.stop="doReject">Reject</a>
                        </span>
                        <span id="meet-in-middle-counter">
                            <a href="" @click.prevent.stop="doCounter">Counter</a>
                        </span>
                    </b-col>
                </b-row>
            </div>
        </b-col>

        <ibar-trade-desired-quantity 
            ref="mimTradePopover" 
            target="meet-in-middle-do-trade" 
            :market-negotiation="marketNegotiation" 
            :open="trade_open" 
            :is-offer="!marketNegotiation.cond_buy_mid" 
            @close="trade_open = false" 
            parent="cond-container">
        </ibar-trade-desired-quantity>

        <ibar-counter-negotiation 
            ref="mimCounterPopover" 
            target="meet-in-middle-counter" 
            :market-negotiation="marketNegotiation" 
            :open="counter_open"
            @close="counter_open = false" 
            parent="trade_app"
            placement="bottom"
            popover-class="popover-meet-in-middle">
        </ibar-counter-negotiation>

    </b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    /*
        cond_buy_mid:
         true  = buy
         false = sell
    */
    export default {
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation
            },
        },
        data() {
            return {
                trade_open: false,
                counter_open: false,
            }
        },
        computed: {
            'type': function() {
                return this.marketNegotiation.cond_buy_mid ? "Sell" : "Buy";
            },
            'value': function() {
                return this.marketNegotiation.cond_buy_mid ? this.marketNegotiation.bid : this.marketNegotiation.offer;
            }
        },
        methods: {
            doTrade() {
                this.trade_open = true;
            },
            doCounter() {
                this.counter_open = true;
            },
            doReject() {
                this.marketNegotiation.killNegotiation()
                .then(response => {
                    console.log(response);
                    this.errors = [];
                })
                .catch(err => {
                    this.errors = err.errors.errors;
                });
            },
        },
        mounted() {
        }
    }
</script>