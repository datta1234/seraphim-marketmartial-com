<template>
    <b-col cols="12">
        <b-row no-gutters>
            <b-col>
                <h4>Untraded Markets</h4>
            </b-col>
        </b-row>
        <b-row class="justify-content-md-center">
            <market-group :key="market.id" v-for="(market, market_index) in $root.display_markets_untraded" :market="market" class="col"></market-group>
        </b-row>
    </b-col>
</template>
<script>
    export default {
        data() { 
            return { 
                
            } 
        },
        mounted() {
            
        }
    };
</script>