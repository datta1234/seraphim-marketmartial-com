<template>
    <b-row dusk="ibar-market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <b-form-input class="w-25" v-model="market_negotiation.bid_qty" type="number" placeholder="Qty"></b-form-input>
                        <b-form-input class="w-25" v-model="market_negotiation.bid" type="text" placeholder="Bid"></b-form-input>
                        <b-form-input class="w-25" v-model="market_negotiation.offer" type="number" placeholder="Offer"></b-form-input>
                        <b-form-input class="w-25" v-model="market_negotiation.offer_qty" type="text" placeholder="Qty"></b-form-input>
                    </b-form>
                </b-col>
                <b-col cols="2">

                </b-col>
            </b-row>

            <b-row class="justify-content-md-center mb-1">
                <b-col>
                    <b-button class="w-100" size="sm" variant="primary">Send</b-button>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col>
                    <b-button class="w-100" size="sm" variant="secondary">No Cares</b-button>
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>

<script>
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';
    export default {
        data() {
            return {
                market_negotiation: new UserMarketNegotiation()
            };
        },
        mounted() {

        }
    }
</script>