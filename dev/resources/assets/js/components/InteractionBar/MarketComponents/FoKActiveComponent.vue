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
                        FoK: {{ fok_value }}
                    </b-col>
                    <b-col>
                        <a href="" v-if="marketNegotiation.cond_fok_apply_bid===null || marketNegotiation.cond_fok_apply_bid===true">Buy</a>
                        <a href="" v-if="marketNegotiation.cond_fok_apply_bid===null || marketNegotiation.cond_fok_apply_bid===false">Sell</a>
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
        computed: {
            fok_value: function() {
                let bid     = this.marketNegotiation.bid,
                    offer   = this.marketNegotiation.offer,
                    cond    = this.marketNegotiation.cond_fok_apply_bid;
                console.log('fok_value', bid, offer, cond);
                switch(cond) {
                    case true:
                        return bid;
                    break;
                    case false:
                        return offer;
                    break;
                    case null:
                        return bid+' / '+offer;
                    break;
                }
                return '';
            }
        },
        methods: {
            
        },
        mounted() {
            
        }
    }
</script>