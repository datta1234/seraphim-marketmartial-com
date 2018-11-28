<template>
    <b-row dusk="ibar-market-negotiation-market" class="ibar market-negotiation">
        <b-col v-if="timed_out" class="text-center">
            <trade-at-best-desired-quantity 
                v-if="rootNegotiation.is_my_org"
                :market-negotiation="currentNegotiation">
            </trade-at-best-desired-quantity>
            <span v-else>
                <div class="text-my-org text-center">
                    {{ tradeAtBestText }}
                </div>
                The Negoitations have timed out, trade imminent.
            </span>
        </b-col>
        <b-col v-if="!timed_out">
            <b-row class="">
                <b-col cols="10">
                    <div class="text-center">
                        <strong>{{ perspective }} {{ term }} at best</strong>
                    </div>
                </b-col>
                <b-col cols="2">
                    <div class="text-right negotiation condition-timer">
                        <strong>{{ timer_value }}</strong>
                    </div>
                </b-col>
            </b-row>
            <b-row class="mb-3" v-if="!rootNegotiation.is_my_org">
                <b-col cols="10">
                    <b-form inline>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.bid_qty" :disabled="disabled || disabled_bid" type="text" dusk="market-negotiation-bid-qty" placeholder="Qty"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.bid" :disabled="disabled || disabled_bid" type="text" dusk="market-negotiation-bid" placeholder="Bid"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.offer" :disabled="disabled || disabled_offer" type="text" dusk="market-negotiation-offer" placeholder="Offer"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.offer_qty" :disabled="disabled || disabled_offer" type="text" dusk="market-negotiation-offer-qty" placeholder="Qty"></b-form-input>
                        </div>
                    </b-form>
                </b-col>
                <b-col cols="2">
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>

<script>
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';
    import UserMarketQuote from '../../../lib/UserMarketQuote';

    import TradeAtBestDesiredQuantity from '../TradeComponents/TradeAtBestDesiredQuantity.vue';
    
    export default {
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation
            },
            rootNegotiation: {
                type: UserMarketNegotiation
            },
            currentNegotiation: {
                type: UserMarketNegotiation
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        components: {
            TradeAtBestDesiredQuantity
        },  
        watch: {
            marketNegotiation: {
                handler() {
                    this.$emit('validate-proposal', this.check_invalid());
                },
                deep: true
            }
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
                timer_value: null,
                disabled_bid: false,
                disabled_offer: false,
            };
        },
        methods: {
            'is_empty': function(value){
                return value === undefined || value === null || value === '';
            },
            'check_invalid':function() {
                return false;
                let invalid_states = {
                    all_empty: false,
                    bid_pair: false,
                    offer_pair: false,
                    previous: false
                };

                //new
                invalid_states.all_empty = 
                        this.is_empty(this.marketNegotiation.bid)
                    &&  this.is_empty(this.marketNegotiation.bid_qty)
                    &&  this.is_empty(this.marketNegotiation.offer)
                    &&  this.is_empty(this.marketNegotiation.offer_qty);

                // Check that bid and bid_qty are present together
                invalid_states.bid_pair = (
                        !this.is_empty(this.marketNegotiation.bid)  
                        &&  this.is_empty(this.marketNegotiation.bid_qty)) 
                    || (
                        this.is_empty(this.marketNegotiation.bid)  
                        && !this.is_empty(this.marketNegotiation.bid_qty)
                    );
             
                // Check bid offer and offer_qty are present together
                invalid_states.offer_pair = ( 
                        !this.is_empty(this.marketNegotiation.offer)  
                        && this.is_empty(this.marketNegotiation.offer_qty)) 
                    || (
                        this.is_empty(this.marketNegotiation.offer)  
                        && !this.is_empty(this.marketNegotiation.offer_qty)
                    );
                
                // Check for previous quote
                if(typeof this.currentNegotiation !== 'undefined' && this.currentNegotiation != null && this.currentNegotiation.is_killed != true) {
                    // Check new currentNegotiation is valid
                    invalid_states.previous = 
                            (
                                !this.is_empty(this.currentNegotiation.bid)
                                && this.marketNegotiation.bid < this.currentNegotiation.bid
                            )
                        // ||  this.marketNegotiation.bid_qty == this.currentNegotiation.bid_qty
                        ||  (
                                !this.is_empty(this.currentNegotiation.offer)
                                && this.marketNegotiation.offer > this.currentNegotiation.offer
                            )
                        // ||  this.marketNegotiation.offer_qty == this.currentNegotiation.offer_qty;
                }
                return invalid_states.all_empty || invalid_states.bid_pair || invalid_states.offer_pair || invalid_states.previous;
            
            },
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
            this.disabled_bid = this.rootNegotiation.cond_buy_best == true;
            this.disabled_offer = this.rootNegotiation.cond_buy_best == false;
        },
        beforeDestroy() {
            this.stopTimer();
        }
    }
</script>