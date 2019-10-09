<template>
    <b-row dusk="ibar-tab-active" class="active-cond-bar" v-if="!timed_out">
        <b-col cols="6" offset="3">
            <div class="text-center condition-toggle">
                <strong>{{ term }} At Best</strong>
            </div>
        </b-col>
        <b-col cols="3">
            <div class="text-right negotiation condition-timer">
                <strong>{{ timer_value }}</strong>
            </div>
        </b-col>
        <b-col cols="12">
            <div v-bind:class="[ isActive ? 'cond-bar-alert' : 'cond-bar-sent' ]">
                <b-row id="cond-container" class="trade-popover">
                    <b-col>
                        {{ term }} at best: {{ trade_value }}
                    </b-col>
                    <b-col v-if="isActive && !$root.is_viewer && !$root.is_admin">
                        <div class="pull-right">
                            <b-row>
                                <b-col>
                                    <span id="tab-popover">
                                        <a  href="" 
                                            @click.prevent.stop="doTrade">
                                                {{ action }}
                                        </a>
                                    </span>
                                </b-col>
                                <b-col>
                                    <span>
                                        <a href="" @click.prevent.stop="doRepeat" v-active-request>Repeat</a>
                                    </span>
                                </b-col>
                            </b-row>
                        </div>
                    </b-col>
                </b-row>
            </div>

            <ibar-trade-desired-quantity
                ref="tabPopover" 
                target="tab-popover" 
                :market-negotiation="negotiation" 
                :open="trade_open" 
                :is-offer="negotiation.cond_buy_best == false" 
                @close="trade_open = false" 
                parent="cond-container">
            </ibar-trade-desired-quantity>
        </b-col>
        <b-col cols="12 mt-1" v-if="$root.is_admin && !negotiation.isTimeoutLocked()">
                    <b-button v-active-request class="admin-condition-btn"  
                              size="sm" 
                              dusk="ibar-condition-end" 
                              variant="primary" 
                              @click="alterTimer('end')">
                                End Timer
                    </b-button>
                    <b-button v-active-request class="float-right admin-condition-btn"  
                              size="sm" 
                              dusk="ibar-condition-reset" 
                              variant="primary" 
                              @click="alterTimer('reset')">
                                Reset Timer
                    </b-button>
            
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
                timed_out: false,
                timer: null,
                timer_value: null,
                trade_open: false,
            }
        },
        computed: {
            term() {
                return this.condition.condition.cond_buy_best ? 'Buy' : 'Sell' ;
            },
            action() {
                return this.condition.condition.cond_buy_best ? 'Sell' : 'Buy' ;
            },
            negotiation() {
                return this.condition.condition;
            },
            trade_value() {
                return this.condition.condition.cond_buy_best ? this.condition.condition.bid : this.condition.condition.offer ;  
            }
        },
        methods: {
            doTrade() {
                this.trade_open = true;
            },
            doRepeat() {
                this.negotiation.repeatNegotiation()
                .then(response => {
                    this.errors = [];
                })
                .catch(err => {
                    this.errors = err.errors;
                });
            },
            alterTimer(option) {
                this.negotiation.alterTradeAtBestTimer(option)
                .then(response => {
                    this.timer_value = this.negotiation.getTimeoutRemaining();
                    this.errors = [];
                })
                .catch(err => {
                    this.errors = err.errors;
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