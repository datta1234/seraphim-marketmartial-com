<template>
    <b-row dusk="ibar-market-negotiation-market" class="ibar market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.bid_qty" type="text" dusk="market-negotiation-bid-qty" placeholder="Qty"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.bid" type="text" dusk="market-negotiation-bid" placeholder="Bid"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.offer" type="text" dusk="market-negotiation-offer" placeholder="Offer"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input class="w-100" v-model="marketNegotiation.offer_qty" type="text" dusk="market-negotiation-offer-qty" placeholder="Qty"></b-form-input>
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
            markerQoute: {
                type: UserMarketQuote
            }
        },
        watch: {
            marketNegotiation: {
                handler:function() {
                    this.$emit('validate-proposal', this.check_invalid);
                },
                deep: true
            }
        },
        data() {
            return {
            };
        },
        computed: {
            'check_invalid':function(){
                let invalid_states = {
                    all_empty: false,
                    bid_pair: false,
                    offer_pair: false,
                    previous: false
                };

                //new
                invalid_states.all_empty = this.is_empty(this.marketNegotiation.bid)
                    && this.is_empty(this.marketNegotiation.bid_qty)
                    && this.is_empty(this.marketNegotiation.offer)
                    && this.is_empty(this.marketNegotiation.offer_qty);

                // Check that bid and bid_qty are present together
                invalid_states.bid_pair = (!this.is_empty(this.marketNegotiation.bid)  
                    && this.is_empty(this.marketNegotiation.bid_qty)) 
                    || (this.is_empty(this.marketNegotiation.bid)  
                    && !this.is_empty(this.marketNegotiation.bid_qty));
             
             
                // Check bid offer and offer_qty are present together
                 invalid_states.offer_pair = ( !this.is_empty(this.marketNegotiation.offer)  
                    && this.is_empty(this.marketNegotiation.offer_qty)) 
                    || (this.is_empty(this.marketNegotiation.offer)  
                    && !this.is_empty(this.marketNegotiation.offer_qty));
                // Check for previous quote
                if(typeof markerQoute != 'undefined') {
                    // Check new markerQoute is equal to old markerQoute
                    invalid_states.previous = this.marketNegotiation.bid == markerQoute.bid
                    && this.marketNegotiation.bid_qty == markerQoute.bid_qty
                    && this.marketNegotiation.offer == markerQoute.offer
                    && this.marketNegotiation.offer_qty == markerQoute.offer_qty;
                }
                
                console.log("markery negoti",this.marketNegotiation.offer,this.marketNegotiation.offer_qty);

                return invalid_states.all_empty || invalid_states.bid_pair || invalid_states.offer_pair || invalid_states.previous;
            
            }
        },
        methods: {
            'is_empty': function(value){
                return value === undefined || value === null || value === '';
            }
        },
        mounted() {
            console.log(this.marketNegotiation);
        }
    }
</script>