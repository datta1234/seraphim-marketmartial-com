<template>
    <b-row dusk="ibar-market-negotiation-market" class="ibar market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request v-input-mask.number.decimal class="w-100" v-model="marketNegotiation.bid_qty" :disabled="disabled_bid || disabled || disable_input" type="text" dusk="market-negotiation-bid-qty" placeholder="Qty"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request 
                                          v-input-mask.number.decimal="{ 
                                            negative: allow_negative(),
                                            negative_callback: setBid
                                          }" 
                                          v-bind:class="{ 'w-100': true, 'self-active-input': active_input_bid }" 
                                          v-model="marketNegotiation.bid" 
                                          :disabled="disabled_bid || disabled || disable_input" 
                                          type="text" 
                                          dusk="market-negotiation-bid" 
                                          placeholder="Bid">
                            </b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request
                                          v-input-mask.number.decimal="{ 
                                            negative: allow_negative(),
                                            negative_callback: setOffer
                                          }"
                                          v-bind:class="{ 'w-100': true, 'self-active-input': active_input_offer }"
                                          v-model="marketNegotiation.offer"
                                          :disabled="disabled_offer || disabled || disable_input" 
                                          type="text" 
                                          dusk="market-negotiation-offer" 
                                          placeholder="Offer">
                            </b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request v-input-mask.number.decimal class="w-100" v-model="marketNegotiation.offer_qty" :disabled="disabled_offer || disabled || disable_input" type="text" dusk="market-negotiation-offer-qty" placeholder="Qty"></b-form-input>
                        </div>
                    </b-form>
                </b-col>
                <b-col cols="2">
                </b-col>
            </b-row>
            
            <b-row v-if="isRequestPhase" class="mb-3">
                <b-col cols="10 text-center">
                    <b-form-group>
                        <b-form-checkbox-group buttons 
                                               button-variant="primary btn-sm"
                                               v-model="bid_offer_selected" 
                                               name="disable-bid-offer" 
                                               :options="bid_offer_options">
                        </b-form-checkbox-group>
                    </b-form-group>
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
            },
            isRequestPhase: {
                type: Boolean,
                default: false    
            },
            negativeBidOffer: {
                type: Boolean,
                default: false    
            },
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
                    if(this.resetting) { 
                        this.buying_at_mid = false;
                        return;
                    }// break early
                    if(nv !== null) {
                        this.buying_at_mid = true;
                        if(ov === null) {
                            this.old.bid = this.marketNegotiation.bid;
                            this.old.offer = this.marketNegotiation.offer;
                        }
                        let bid = this.currentNegotiation.bid;
                        let offer = this.currentNegotiation.offer;
                        this.marketNegotiation.bid = this.marketNegotiation.offer = ( bid + offer ) / 2;
                        this.disable_input = true;
                    } else {
                        this.buying_at_mid = false;
                        this.marketNegotiation.bid = this.old.bid;
                        this.marketNegotiation.offer = this.old.offer;
                        this.disable_input = false;
                    }
                });
            },
            'disabled_bid'(nv, ov) {
                this.$nextTick(() => {
                    if(this.resetting || this.buying_at_mid == true) { return }; // break early
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
                    if(this.resetting || this.buying_at_mid == true) { return }; // break early
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
                buying_at_mid: false,
                disable_input: false,
                force_invalid: false,
                old: {
                    resetting: false,
                    bid: null,
                    offer: null,
                    bid_qty: null,
                    offer_qty: null
                },
                bid_offer_options: [
                    { text: 'Bid Only', value: 'bid-only' },
                    { text: 'Offer Only', value: 'offer-only' },
                ],
                bid_offer_selected: [],
            };
        },
        computed: {
            active_input_bid: function() {
                let old = this.currentNegotiation ? this.currentNegotiation.bid : null;
                let current = this.marketNegotiation.bid;
                return old !== null && current !== null && current !== '' && old != current
            },
            active_input_offer: function() {
                let old = this.currentNegotiation ? this.currentNegotiation.offer : null;
                let current = this.marketNegotiation.offer;
                return old !== null && current !== null && current !== '' && old != current
            },
            disabled_bid: function() {
                // if both parents are spin and offer has value disabled
                let spun = this.currentNegotiation && this.currentNegotiation.isSpun();
                let traded = this.currentNegotiation && this.currentNegotiation.isTraded();
                let no_trade = this.currentNegotiation && this.currentNegotiation.isNoTrade();
                let amending = this.currentNegotiation && this.currentNegotiation.id == this.marketNegotiation.id;
                let value = this.marketNegotiation.offer;
                let only = (this.bid_offer_selected.includes('offer-only') && this.isRequestPhase );

                // if offer only is selected
                if( only ) {
                    return true;
                }

                if(amending) {
                    return !this.currentNegotiation.getAmountSource('bid').is_my_org;
                }

                if((traded || spun || no_trade) && value) {
                    return true;
                }
                return false;
            },
            disabled_offer: function() {
                // if both parents are spin and bid has value disabled
                let spun = this.currentNegotiation && this.currentNegotiation.isSpun();
                let traded = this.currentNegotiation && this.currentNegotiation.isTraded();
                let no_trade = this.currentNegotiation && this.currentNegotiation.isNoTrade();
                let amending = this.currentNegotiation && this.currentNegotiation.id == this.marketNegotiation.id;
                let value = this.marketNegotiation.bid;
                let only = (this.bid_offer_selected.includes('bid-only') && this.isRequestPhase);

                // if bid only is selected
                if( only ) {
                    return true;
                }

                if(amending) {
                    return !this.currentNegotiation.getAmountSource('offer').is_my_org;
                }

                if((traded || spun || no_trade) && value) {
                    return true;
                }
                return false;
            },
        },
        methods: {
            allow_negative: function() {
                return this.negativeBidOffer;
            },
            setBid: function(value) {
                if(value && value !== this.marketNegotiation.bid) {
                    this.marketNegotiation.bid = value;
                }
            },
            setOffer: function(value) {
                if(value && value !== this.marketNegotiation.offer) {
                    this.marketNegotiation.offer = value;
                }
            },
            'is_empty': function(value) {
                return value === undefined || value == null || value == '';
            },
            'check_invalid':function() {
                if(this.force_invalid == true) {
                    return true;
                }

                let invalid_states = {
                    all_empty: false,
                    bid_pair: false,
                    offer_pair: false,
                    previous: false
                };

                let traded = this.currentNegotiation && this.currentNegotiation.isTraded();
                let no_trade = this.currentNegotiation && this.currentNegotiation.isNoTrade();
                if(traded || no_trade) {
                    return false;
                }

                //new
                invalid_states.all_empty = 
                        this.is_empty(this.marketNegotiation.bid)
                    &&  this.is_empty(this.marketNegotiation.bid_qty)
                    &&  this.is_empty(this.marketNegotiation.offer)
                    &&  this.is_empty(this.marketNegotiation.offer_qty);

                
                
                // Check for previous quote
                if(typeof this.currentNegotiation !== 'undefined' && this.currentNegotiation != null) {
                    if(this.currentNegotiation.is_killed == true) {
                        let currentBid = this.currentNegotiation.getAmountSource('bid');
                        let currentOffer = this.currentNegotiation.getAmountSource('offer');
                        // Check new currentNegotiation is valid
                        invalid_states.previous = 
                                (
                                    !this.is_empty(this.marketNegotiation.bid)
                                    && parseFloat(this.marketNegotiation.bid) >= parseFloat(currentOffer.offer)
                                )
                            // ||  this.marketNegotiation.bid_qty == this.currentNegotiation.bid_qty
                            ||  (
                                    !this.is_empty(this.marketNegotiation.offer)
                                    && parseFloat(this.marketNegotiation.offer) <= parseFloat(currentBid.bid)
                                )

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
                    } else {
                        let currentBid = this.currentNegotiation.getAmountSource('bid');
                        let currentOffer = this.currentNegotiation.getAmountSource('offer');
                        // Check new currentNegotiation is valid
                        invalid_states.previous = 
                                (
                                    this.currentNegotiation.bid != null
                                    && !this.is_empty(this.marketNegotiation.bid)
                                    && ( 
                                        parseFloat(this.marketNegotiation.bid) < parseFloat(currentBid.bid)
                                        || parseFloat(this.marketNegotiation.bid) >= parseFloat(currentOffer.offer)
                                    )
                                )
                            // ||  this.marketNegotiation.bid_qty == this.currentNegotiation.bid_qty
                            ||  (
                                    this.currentNegotiation.offer != null
                                    && !this.is_empty(this.marketNegotiation.offer)
                                    && (
                                        parseFloat(this.marketNegotiation.offer) > parseFloat(currentOffer.offer)
                                        || parseFloat(this.marketNegotiation.offer) <= parseFloat(currentBid.bid)
                                    )
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
                    }

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
                        ) || (
                            !this.is_empty(this.marketNegotiation.offer)
                            && parseFloat(this.marketNegotiation.offer) <= parseFloat(this.marketNegotiation.bid)
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
                        ) || (
                            !this.is_empty(this.marketNegotiation.bid)
                            && parseFloat(this.marketNegotiation.offer) <= parseFloat(this.marketNegotiation.bid)
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
                this.bid_offer_selected = [];
            }
        },
        mounted() {
            this.reset();
            EventBus.$on('resetStarted', () => {
                this.resetting = true;
            });
            EventBus.$on('resetComplete', this.reset);
            EventBus.$on('negotiationInputDisabled', () => {
                this.disable_input = true;
            });
            EventBus.$on('negotiationInputEnabled', () => {
                this.disable_input = false;
            });

            EventBus.$on('sendDisabled', () => {
                this.force_invalid = true;
            });
            EventBus.$on('sendEnabled', () => {
                this.force_invalid = false;
            });
            
        }
    }
</script>