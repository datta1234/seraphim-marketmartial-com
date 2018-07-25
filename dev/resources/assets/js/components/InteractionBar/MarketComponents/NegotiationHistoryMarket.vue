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
                            <b-btn v-bind:class="{'active': item.is_on_hold}" 
                                size="sm" 
                                class="w-100" 
                                @click="putQuoteOnHold(item)"
                                variant="secondary">
                                    HOLD
                            </b-btn>
                        </b-col>
                        <b-col cols="3" class="text-center">
                            <b-btn size="sm" class="w-100" variant="primary">ACCEPT</b-btn>
                        </b-col>
                    </b-row>
                    <b-row no-gutters v-else-if="item.is_maker">
                        <b-col cols="3" class="text-center">
                            {{ item.bid_qty }}
                        </b-col>
                        <b-col cols="3" class="text-center">
                            {{ item.bid }}
                        </b-col>
                        <b-col cols="3" class="text-center">
                            {{ item.offer }}
                        </b-col>
                        <b-col cols="3" class="text-center">
                            {{ item.offer_qty }}
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
            <b-row class="justify-content-md-center" v-if="history.reduce((x,y) => x = y.is_interest, false)">
                <b-col class="mt-2">
                    <p>
                        <small>Note: All quotes will default to HOLD after 30 minutes from the receipt of response has lapsed.</small>
                    </p>
                </b-col>
            </b-row>
               <b-row class="justify-content-md-center" v-if="message">
                <b-col class="mt-2">
                    <p>
                        <small>{{ message }}</small>
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
            },
            message:{
                type: String
            }
        },
        data() {
            return {

            };
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
            },
            putQuoteOnHold(quote) {
                if(!quote.is_on_hold) {
                    let save_quote = quote.putOnHold();
                }
            }
        },
        mounted() {
            
        }
    }
</script>