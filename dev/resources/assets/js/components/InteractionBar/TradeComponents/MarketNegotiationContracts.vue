<template>
    <b-row dusk="ibar-market-negotiation-contracts" class="ibar market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.bid_qty" type="text" placeholder="Qty"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.bid" type="text" placeholder="Bid"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.offer" type="text" placeholder="Offer"></b-form-input>
                        </div>
                        <div class="w-25 p-1">
                            <b-form-input v-active-request class="w-100" v-model="marketNegotiation.offer_qty" type="text" placeholder="Qty"></b-form-input>
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
            // console.log(this.marketNegotiation);
        }
    }
</script>