<template>
    <b-row dusk="ibar-fok-active" class="active-cond-bar">
        <b-col cols="11" offset="1">
            <div class="text-right condition">
                <strong>20:00</strong>
            </div>
        </b-col>
        <b-col cols="12">
            <div class="cond-bar">
                <b-row id="cond-container" class="trade-popover">
                    <b-col>
                        FoK: {{ fok_value }}
                    </b-col>
                    <b-col>
                        <span id="fok-popover-hit">
                            <a  href="" 
                                @click.prevent.stop="doBuy" 
                                v-if="marketNegotiation.cond_fok_apply_bid===null || marketNegotiation.cond_fok_apply_bid===false">
                                    Buy
                            </a>
                        </span>
                        <span id="fok-popover-lift">
                            <a  href="" 
                                @click.prevent.stop="doSell" 
                                v-if="marketNegotiation.cond_fok_apply_bid===null || marketNegotiation.cond_fok_apply_bid===true">
                                    Sell
                            </a>
                        </span>
                        <span>
                            <a href="" @click.prevent.stop="doKill">Kill</a>
                        </span>
                    </b-col>
                </b-row>
            </div>

            <ibar-trade-desired-quantity 
                v-if="marketNegotiation.cond_fok_apply_bid===null || marketNegotiation.cond_fok_apply_bid===false" 
                ref="fokPopoverHit" 
                target="fok-popover-hit" 
                :market-negotiation="marketNegotiation" 
                :open="bid_sell" 
                :is-offer="false" 
                @close="bid_sell = false" 
                parent="fok-container">
            </ibar-trade-desired-quantity>

            <ibar-trade-desired-quantity 
                v-if="marketNegotiation.cond_fok_apply_bid===null || marketNegotiation.cond_fok_apply_bid===true" 
                ref="fokPopoverLift" 
                target="fok-popover-lift" 
                :market-negotiation="marketNegotiation" 
                :open="offer_buy" 
                :is-offer="true" 
                @close="offer_buy = false" 
                parent="fok-container">
            </ibar-trade-desired-quantity>

        </b-col>
    </b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';

    export default {
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation
            },
        },
        data() {
            return {
                bid_sell: false,
                offer_buy: false,
            }
        },
        computed: {
            fok_value: function() {
                let bid     = this.marketNegotiation.bid,
                    offer   = this.marketNegotiation.offer,
                    cond    = this.marketNegotiation.cond_fok_apply_bid;
                console.log('fok_value', bid, offer, cond);
                switch(cond) {
                    case true:
                        return bid;
                    break;
                    case false:
                        return offer;
                    break;
                    case null:
                        return bid+' / '+offer;
                    break;
                }
                return '';
            }
        },
        methods: {
            doBuy() {
                // this.marketNegotiation.fokBuy();
                this.bid_sell = true;
            },
            doSell() {
                // this.marketNegotiation.sell();
                this.offer_buy = true;
            },
            doKill() {
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