<template>
    <b-col cols="12">
        <b-row no-gutters>
            <b-col>
                <h3>Traded Markets</h3>
            </b-col>
        </b-row>
        <b-row class="justify-content-md-center">
            <market-group :key="market.id" v-for="(market, market_index) in markets" :market="market" class="col" :force-show="true"></market-group>
        </b-row>
    </b-col>
</template>
<script>
    export default {
        props: {
            markets: {
                type: Array
            }
        },
        data() { 
            return { 
                
            } 
        },
        computed: {
            has_requests: function() {
                return this.markets.reduce((out,market) => {
                    if(market.market_requests.length > 0) {
                        out = true;
                    }
                    return out;
                }, false);
            }
        },
        mounted() {
            
        }
    };
</script>