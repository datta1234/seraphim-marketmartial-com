<template>
    <b-row dusk="ibar-fok-active" class="active-fok-bar">
        <b-col cols="11" offset="1">
            <div class="text-right condition">
                <strong>20:00</strong>
            </div>
        </b-col>
        <b-col cols="12">
            <div class="fok-bar">
                <b-row>
                    <b-col>
                        FoK: {{ (marketNegotiation.cond_fok_apply_bid ? marketNegotiation.bid : marketNegotiation.offer ) }}
                    </b-col>
                    <b-col>
                        <a href="" v-if="marketNegotiation.cond_fok_apply_bid==true">Buy</a>
                        <a href="" v-else>Sell</a>
                        <a href="">Kill</a>
                    </b-col>
                </b-row>
            </div>
        </b-col>
    </b-row>
</template>
<script>
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';

    export default {
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation
            },
        },
        data() {
            return {
                
            }
        },
        methods: {
            
        },
        mounted() {
            
        }
    }
</script>