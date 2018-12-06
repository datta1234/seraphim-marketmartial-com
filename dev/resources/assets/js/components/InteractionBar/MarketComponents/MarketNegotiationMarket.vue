<template>
    <b-row dusk="ibar-market-negotiation-market" class="ibar market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.bid_qty" :disabled="disabled_bid || disabled" type="text" dusk="market-negotiation-bid-qty" placeholder="Qty"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.bid" :disabled="disabled_bid || disabled" type="text" dusk="market-negotiation-bid" placeholder="Bid"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.offer" :disabled="disabled_offer || disabled" type="text" dusk="market-negotiation-offer" placeholder="Offer"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.offer_qty" :disabled="disabled_offer || disabled" type="text" dusk="market-negotiation-offer-qty" placeholder="Qty"></b-form-input>
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
    import { EventBus } from '~/lib/EventBus.js';
    
    export default {
        props: {
            marketNegotiation: {
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
        watch: {
            marketNegotiation: {
                handler:function() {
                    this.$emit('validate-proposal', this.check_invalid());
                },
                deep: true
            },
            'marketNegotiation.cond_buy_mid'(nv, ov) {
                this.$nextTick(() => {
                    if(this.resetting) { return }; // break early
                    console.log("Changed: ", nv)
                    if(nv !== null) {
                        if(ov === null) {
                            this.old.bid = this.marketNegotiation.bid;
                            this.old.offer = this.marketNegotiation.offer;
                        }
                        this.marketNegotiation.bid = this.marketNegotiation.offer = ( this.currentNegotiation.bid + this.currentNegotiation.offer ) / 2;
                    } else {
                        this.marketNegotiation.bid = this.old.bid;
                        this.marketNegotiation.offer = this.old.offer;
                    }
                });
            },
            'disabled_bid'(nv, ov) {
                this.$nextTick(() => {
                    if(this.resetting) { return }; // break early
                    console.log("Changed 2: ", nv)
                    if(nv) {
                        if(this.marketNegotiation.bid_qty) {
                            this.old.bid = this.marketNegotiation.bid;
                            this.old.bid_qty = this.marketNegotiation.bid_qty;
                            this.marketNegotiation.bid = null;
                            this.marketNegotiation.bid_qty = null;
                        }
                    } else {
                        this.marketNegotiation.bid_qty = this.old.bid_qty;
                        this.marketNegotiation.bid = this.old.bid;
                    }
                });
            },
            'disabled_offer'(nv, ov) {
                this.$nextTick(() => {
                    if(this.resetting) { return }; // break early
                    console.log("Changed 3: ", nv)
                    if(nv) {
                        if(this.marketNegotiation.offer_qty) {
                            this.old.offer = this.marketNegotiation.offer;
                            this.old.offer_qty = this.marketNegotiation.offer_qty;
                            this.marketNegotiation.offer = null;
                            this.marketNegotiation.offer_qty = null;
                        }
                    } else {
                        this.marketNegotiation.offer_qty = this.old.offer_qty;
                        this.marketNegotiation.offer = this.old.offer;
                    }
                });
            }
        },
        data() {
            return {
                old: {
                    resetting: false,
                    bid: null,
                    offer: null,
                    bid_qty: null,
                    offer_qty: null
                }
            };
        },
        computed: {
            disabled_bid: function() {
                // if both parents are spin and offer has value disabled
                let spun = this.currentNegotiation && this.currentNegotiation.isSpun();
                let traded = this.currentNegotiation && this.currentNegotiation.isTraded();

                let amending = this.currentNegotiation && this.currentNegotiation.id == this.marketNegotiation.id;
                if(amending) {
                    return !this.currentNegotiation.getAmountSource('bid').is_my_org;
                }

                let value = this.marketNegotiation.offer;
                if((traded || spun) && value) {
                    return true;
                }
                return false;
            },
            disabled_offer: function() {
                // if both parents are spin and bid has value disabled
                let spun = this.currentNegotiation && this.currentNegotiation.isSpun();
                let traded = this.currentNegotiation && this.currentNegotiation.isTraded();

                let amending = this.currentNegotiation && this.currentNegotiation.id == this.marketNegotiation.id;
                if(amending) {
                    return !this.currentNegotiation.getAmountSource('offer').is_my_org;
                }

                let value = this.marketNegotiation.bid;
                if((traded || spun) && value) {
                    return true;
                }
                return false;
            },
        },
        methods: {
            'is_empty': function(value) {
                return value === undefined || value == null || value == '';
            },
            'check_invalid':function() {
                let invalid_states = {
                    all_empty: false,
                    bid_pair: false,
                    offer_pair: false,
                    previous: false
                };

                let traded = this.currentNegotiation && this.currentNegotiation.isTraded();
                if(traded) {
                    return false;
                }

                //new
                invalid_states.all_empty = 
                        this.is_empty(this.marketNegotiation.bid)
                    &&  this.is_empty(this.marketNegotiation.bid_qty)
                    &&  this.is_empty(this.marketNegotiation.offer)
                    &&  this.is_empty(this.marketNegotiation.offer_qty);

                
                
                // Check for previous quote
                if(typeof this.currentNegotiation !== 'undefined' && this.currentNegotiation != null && this.currentNegotiation.is_killed != true) {
                    let currentBid = this.currentNegotiation.getAmountSource('bid');
                    let currentOffer = this.currentNegotiation.getAmountSource('offer');
                    // Check new currentNegotiation is valid
                    invalid_states.previous = 
                            (
                                this.currentNegotiation.bid != null
                                && !this.is_empty(this.marketNegotiation.bid)
                                && parseFloat(this.marketNegotiation.bid) < parseFloat(currentBid.bid)
                            )
                        // ||  this.marketNegotiation.bid_qty == this.currentNegotiation.bid_qty
                        ||  (
                                this.currentNegotiation.offer != null
                                && !this.is_empty(this.marketNegotiation.offer)
                                && parseFloat(this.marketNegotiation.offer) > parseFloat(currentOffer.offer)
                            )
                        // ||  this.marketNegotiation.offer_qty == this.currentNegotiation.offer_qty;

                    // Check that bid and bid_qty are present together
                    invalid_states.bid_pair = this.currentNegotiation.bid != null && (
                        (
                            !this.is_empty(this.marketNegotiation.bid)  
                            &&  this.is_empty(this.marketNegotiation.bid_qty)
                        ) || (
                            this.is_empty(this.marketNegotiation.bid)  
                            && !this.is_empty(this.marketNegotiation.bid_qty)
                        )
                    );
                 
                    // Check bid offer and offer_qty are present together
                    invalid_states.offer_pair = this.currentNegotiation.offer != null && (
                        ( 
                            !this.is_empty(this.marketNegotiation.offer)  
                            && this.is_empty(this.marketNegotiation.offer_qty)
                        ) || (
                            this.is_empty(this.marketNegotiation.offer)  
                            && !this.is_empty(this.marketNegotiation.offer_qty)
                        )
                    );

                } else {
                    // Quote
                    // Check that bid and bid_qty are present together
                    invalid_states.bid_pair = (
                        (
                            !this.is_empty(this.marketNegotiation.bid)  
                            &&  this.is_empty(this.marketNegotiation.bid_qty)
                        ) || (
                            this.is_empty(this.marketNegotiation.bid)  
                            && !this.is_empty(this.marketNegotiation.bid_qty)
                        )
                    );
                 
                    // Check bid offer and offer_qty are present together
                    invalid_states.offer_pair = (
                        ( 
                            !this.is_empty(this.marketNegotiation.offer)  
                            && this.is_empty(this.marketNegotiation.offer_qty)
                        ) || (
                            this.is_empty(this.marketNegotiation.offer)  
                            && !this.is_empty(this.marketNegotiation.offer_qty)
                        )
                    );
                }
                return invalid_states.all_empty || invalid_states.bid_pair || invalid_states.offer_pair || invalid_states.previous;
            
            },
            reset() {
                this.old.bid = null;
                this.old.offer = null;
                this.old.offer_qty = this.marketNegotiation.offer_qty;
                this.old.bid_qty = this.marketNegotiation.bid_qty;
                this.resetting = false;
            }
        },
        mounted() {
            this.reset();
            EventBus.$on('resetStarted', () => {
                this.resetting = true;
            });
            EventBus.$on('resetComplete', this.reset);
        }
    }
</script>