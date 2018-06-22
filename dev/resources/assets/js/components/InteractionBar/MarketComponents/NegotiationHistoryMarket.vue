<template>
    <b-row dusk="ibar-negotiation-history-market">
        <b-col>
            <b-row v-for="item in history">            
                <b-col cols="10">
                    <b-row no-gutters v-if="item.is_interest">
                        <b-col cols="6" class="text-center">
                            {{ getState(item) }}
                        </b-col>
                        <b-col cols="3" class="text-center">
                            <b-btn size="sm" class="w-100" variant="secondary">HOLD</b-btn>
                        </b-col>
                        <b-col cols="3" class="text-center">
                            <b-btn size="sm" class="w-100" variant="primary">ACCEPT</b-btn>
                        </b-col>
                    </b-row>
                    <b-row no-gutters v-else>
                        <b-col cols="12" class="text-center">
                            {{ getState(item) }}
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="2">
                    <p class="text-center">
                        <small>{{ item.time }}</small>
                    </p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col class="mt-2">
                    <p>
                        <small>Note: All quotes will default to HOLD after 30 minutes from the receipt of response has lapsed.</small>
                    </p>
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>

<script>
    export default {
        props: {
            history: {
                type: Array
            }
        },
        methods: {
            getState(item) {
                if(item.bid_only) {
                    return "BID ONLY";
                }
                if(item.offer_only) {
                    return "OFFER ONLY";
                }
                if(item.vol_spread != null) {
                    return item.vol_spread+" VOL SPREAD";
                }
                return "";
            }
        },
        mounted() {
            
        }
    }
</script>