<template>
    <b-row dusk="ibar-fok-active" class="active-cond-bar">
        <b-col cols="11" offset="1">
            <div class="text-right negotiation condition-timer">
                <strong>{{ timer_value }}</strong>
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
                                v-if="negotiation.cond_fok_apply_bid===null || negotiation.cond_fok_apply_bid===false">
                                    Buy
                            </a>
                        </span>
                        <span id="fok-popover-lift">
                            <a  href="" 
                                @click.prevent.stop="doSell" 
                                v-if="negotiation.cond_fok_apply_bid===null || negotiation.cond_fok_apply_bid===true">
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
                v-if="negotiation.cond_fok_apply_bid===null || negotiation.cond_fok_apply_bid===false" 
                ref="fokPopoverHit" 
                target="fok-popover-hit" 
                :market-negotiation="negotiation" 
                :open="bid_sell" 
                :is-offer="false" 
                @close="bid_sell = false" 
                parent="fok-container">
            </ibar-trade-desired-quantity>

            <ibar-trade-desired-quantity 
                v-if="negotiation.cond_fok_apply_bid===null || negotiation.cond_fok_apply_bid===true" 
                ref="fokPopoverLift" 
                target="fok-popover-lift" 
                :market-negotiation="negotiation" 
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
    import ActiveCondition from '~/lib/ActiveCondition';

    export default {
        props: {
            condition: {
                type: ActiveCondition
            },
        },
        data() {
            return {
                timer: null,
                timer_value: null,
                bid_sell: false,
                offer_buy: false,
            }
        },
        computed: {
            negotiation() {
                return this.condition.condition;
            },
            fok_value: function() {
                let bid     = this.negotiation.bid,
                    offer   = this.negotiation.offer,
                    cond    = this.negotiation.cond_fok_apply_bid;
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
                // this.negotiation.fokBuy();
                this.bid_sell = true;
            },
            doSell() {
                // this.negotiation.sell();
                this.offer_buy = true;
            },
            doKill() {
                this.negotiation.killNegotiation()
                .then(response => {
                    this.errors = [];
                })
                .catch(err => {
                    this.errors = err.errors.errors;
                });
            },
            startTimer() {
                if(this.timer != null) {
                    this.stopTimer();
                }
                this.timer = setInterval(() => {
                    let time = moment(this.negotiation.created_at).add(20, 'minutes');
                    let diff = time.diff(moment());
                    let dur = moment.duration(diff);
                    this.timer_value = moment.utc(dur.as('milliseconds')).format('mm:ss');
                }, 1000);
            },
            stopTimer() {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        mounted() {
            this.startTimer();
        },
        beforeDestroy() {
            this.stopTimer();
        }
    }
</script>