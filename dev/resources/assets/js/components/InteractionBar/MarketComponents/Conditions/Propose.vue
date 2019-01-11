<template>
    <b-container fluid>
        <b-row>
            <b-col class="text-center mb-2">
                <strong>Proposed Level:</strong>
            </b-col>
        </b-row>
        <b-row>
            <b-col class="text-center">
                <p>
                    <b-form-input v-active-request class="input-small" v-model="marketNegotiation.bid" :disabled="bid_disabled" type="text" placeholder="Bid"></b-form-input>
                </p>
            </b-col>
            <b-col class="text-center">
                <p>
                    <b-form-input v-active-request class="input-small" v-model="marketNegotiation.offer" :disabled="offer_disabled" type="text" placeholder="Offer"></b-form-input>
                </p>
            </b-col>
        </b-row>
    </b-container>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import { EventBus } from '~/lib/EventBus';
    export default {
        name: 'condition-propose',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            },
            condition: {
                default: null
            }
        },
        data() {
            return {
                old: {
                    bid_qty: null,
                    offer_qty: null,
                }
            }
        },
        computed: {
            bid_disabled: function() {
                let disabled = this.marketNegotiation.offer != "" && this.marketNegotiation.offer != null;
                if(disabled) {
                    if(this.marketNegotiation.bid_qty != null) {
                        this.old.bid_qty = this.marketNegotiation.bid_qty;
                        this.marketNegotiation.bid_qty = null;
                    }
                } else {
                    if(this.old.bid_qty != null) {
                        this.marketNegotiation.bid_qty = this.old.bid_qty;
                    }
                }
                return disabled;
            },
            offer_disabled: function() {
                let disabled =  this.marketNegotiation.bid != "" && this.marketNegotiation.bid != null;
                if(disabled) {
                    if(this.marketNegotiation.offer_qty != null) {
                        this.old.offer_qty = this.marketNegotiation.offer_qty;
                        this.marketNegotiation.offer_qty = null;
                    }
                } else {
                    if(this.old.offer_qty != null) {
                        this.marketNegotiation.offer_qty = this.old.offer_qty;
                    }
                }
                return disabled;
            }
        },
        mounted() {
            // clear the inputs for one sided input only
            this.marketNegotiation.bid = "";
            this.marketNegotiation.offer = "";

            // set the quantitites if there arent any
            if(this.marketNegotiation.bid_qty == null) {
                this.old.bid_qty = this.marketNegotiation.getUserMarket().getMarketRequest().default_quantity;
            }
            if(this.marketNegotiation.offer_qty == null) {
                this.old.offer_qty = this.marketNegotiation.getUserMarket().getMarketRequest().default_quantity;
            }
        },
        beforeMount() {
            EventBus.$emit('disableNegotiationInput');
        },
        beforeDestroy() {
            EventBus.$emit('enableNegotiationInput');
        }
    }
</script>