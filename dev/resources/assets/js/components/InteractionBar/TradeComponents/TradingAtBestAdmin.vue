<template>
    <b-row dusk="ibar-trading-at-best-admin" class="ibar market-negotiation" v-if="!timed_out">
        <b-col cols="6" offset="3" >
            <div class="text-center">
                <strong>{{ perspective }} {{ term }} at best</strong>
            </div>
        </b-col>
        <b-col cols="3">
            <div class="text-right negotiation condition-timer">
                <strong>{{ timer_value }}</strong>
            </div>
        </b-col>
        <b-col cols="12 mt-1">
            <time-condition-admin-actions
                :market-negotiation="currentNegotiation"
                :timed-out="timed_out">
            </time-condition-admin-actions>
        </b-col>
    </b-row>
</template>

<script>
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';
    import TimeConditionAdminActions from '../Components/TimeConditionAdminActions';
    
    export default {
        props: {
            rootNegotiation: {
                type: UserMarketNegotiation
            },
            currentNegotiation: {
                type: UserMarketNegotiation
            }
        },
        components: {
            TimeConditionAdminActions
        },
        computed: {
            tradeAtBestText() {
                let side = this.rootNegotiation.cond_buy_best ? "offer" : "bid";
                let level = this.currentNegotiation[side];
                return "Trading @ "+level;
            },
            perspective() {
                return this.rootNegotiation.is_my_org ? "You are"
                    : (this.rootNegotiation.cond_buy_best ? "The Bid is" : "The Offer is");
            },
            term() {
                return this.rootNegotiation.cond_buy_best ? 'Buying' : 'Selling' ;
            }
        },
        data() {
            return {
                timed_out: false,
                timer: null,
                timer_value: null
            };
        },
        methods: {
            startTimer() {
                if(this.timer != null) {
                    this.stopTimer();
                }
                this.timer = setInterval(this.runTimer, 1000);
            },
            runTimer() {
                this.timer_value = this.currentNegotiation.getTimeoutRemaining();
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