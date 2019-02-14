<template>
    <b-row dusk="ibar-fok-active" class="active-cond-bar">
        <b-col cols="11" offset="1">
            <div class="text-right negotiation condition-timer">
                <strong>{{ timer_value }}</strong>
            </div>
        </b-col>
        <b-col cols="12">
            <div v-bind:class="[ isActive ? 'cond-bar-alert' : 'cond-bar-sent' ]">
                <b-row id="cond-container" class="trade-popover">
                    <b-col>
                        {{ fok_value }}
                    </b-col>
                    <b-col v-if="isActive && !$root.is_viewer">
                        <b-row>
                            <b-col id="fok-popover-hit">
                                <a  href="" 
                                    @click.prevent.stop="doTrade">
                                        Trade
                                </a>
                            </b-col>
                            <b-col>
                                <a href="" @click.prevent.stop="doKill">Kill</a>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </div>

            <ibar-trade-desired-quantity 
                ref="fokPopoverHit" 
                target="fok-popover-hit" 
                :market-negotiation="negotiation" 
                :open="do_trade" 
                :is-offer="null" 
                @close="do_trade = false"
                parent="cond-container">
            </ibar-trade-desired-quantity>

        </b-col>
    </b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import ActiveCondition from '~/lib/ActiveCondition';
    import SentCondition from '~/lib/SentCondition';

    export default {
        props: {
            condition: {
                type: [ActiveCondition,SentCondition]
            },
            isActive: {
                type: Boolean,
                default: true,
            },
        },
        data() {
            return {
                timer: null,
                timer_value: null,
                do_trade: false,
            }
        },
        computed: {
            negotiation() {
                return this.condition.condition;
            },
            fok_value: function() {
                console.log(this.negotiation);
                let bid     = this.negotiation.bid,
                    offer   = this.negotiation.offer,
                    cond    = this.negotiation.cond_fok_apply_bid;

                switch(cond) {
                    case true:
                        return "Bid - FoK:"+ bid ;
                    break;
                    case false:
                        return "Offer - FoK:"+offer;
                    break;
                    case null:
                        return "Bid/Offer - FoK:"+bid+' / '+offer;
                    break;
                }
                return '';
            }
        },
        methods: {
            doTrade() {
                // this.negotiation.fokBuy();
                this.do_trade = true;
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
                this.timer = setInterval(this.runTimer, 1000);
            },
            runTimer() {
                this.timer_value = this.negotiation.getTimeoutRemaining();
                if(this.timer_value == "00:00") {
                    this.timed_out = true;
                    this.stopTimer();
                }
            },
            stopTimer() {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        mounted() {
            this.startTimer();
            this.runTimer(); // force initial setting
        },
        beforeDestroy() {
            this.stopTimer();
        }
    }
</script>