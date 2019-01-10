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
        computed: {
            bid_disabled: function() {
                return this.marketNegotiation.offer != "";
            },
            offer_disabled: function() {
                return this.marketNegotiation.bid != "";
            }
        },
        mounted() {
            // clear the inputs for one sided input only
            this.marketNegotiation.bid = "";
            this.marketNegotiation.offer = "";
        },
        beforeMount() {
            EventBus.$emit('disableNegotiationInput');
        },
        beforeDestroy() {
            EventBus.$emit('enableNegotiationInput');
        }
    }
</script>