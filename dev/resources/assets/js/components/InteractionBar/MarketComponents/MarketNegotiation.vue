<template>
    <b-row dusk="ibar-market-negotiation" class="ibar market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <b-form-input class="w-25" v-model="marketNegotiation.bid_qty" type="number" placeholder="Qty"></b-form-input>
                        <b-form-input class="w-25" v-model="marketNegotiation.bid" type="text" placeholder="Bid"></b-form-input>
                        <b-form-input class="w-25" v-model="marketNegotiation.offer" type="number" placeholder="Offer"></b-form-input>
                        <b-form-input class="w-25" v-model="marketNegotiation.offer_qty" type="text" placeholder="Qty"></b-form-input>
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

    export default {
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation
            }
        },
        data() {
            return {
            };
        },
        mounted() {
            console.log(this.marketNegotiation);
        }
    }
</script>