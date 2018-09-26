<template>
    <b-row dusk="ibar-market-negotiation-market" class="ibar market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.bid_qty" :disabled="disabled" type="text" dusk="market-negotiation-bid-qty" placeholder="Qty"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.bid" :disabled="disabled" type="text" dusk="market-negotiation-bid" placeholder="Bid"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.offer" :disabled="disabled" type="text" dusk="market-negotiation-offer" placeholder="Offer"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.offer_qty" :disabled="disabled" type="text" dusk="market-negotiation-offer-qty" placeholder="Qty"></b-form-input>
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
            }
        },
        data() {
            return {
            };
        },
        methods: {
            'is_empty': function(value){
                return value === undefined || value === null || value === '';
            },
            'check_invalid':function() {
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
                                !this.is_empty(this.marketNegotiation.bid)
                                && this.marketNegotiation.bid < this.currentNegotiation.bid
                            )
                        // ||  this.marketNegotiation.bid_qty == this.currentNegotiation.bid_qty
                        ||  (
                                !this.is_empty(this.marketNegotiation.offer)
                                && this.marketNegotiation.offer > this.currentNegotiation.offer
                            )
                        // ||  this.marketNegotiation.offer_qty == this.currentNegotiation.offer_qty;
                }
                console.log(invalid_states);
                return invalid_states.all_empty || invalid_states.bid_pair || invalid_states.offer_pair || invalid_states.previous;
            
            }
        },
        mounted() {
            console.log(this.disabled);
        }
    }
</script>