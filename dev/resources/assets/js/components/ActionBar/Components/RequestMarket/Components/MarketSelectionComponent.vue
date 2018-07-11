<template>
    <div dusk="market-selection" class="step-selections">
            market selection

        <b-container fluid>
            <b-row class="text-center">
                <b-col v-if="data" v-for="market in data.market_type.markets" cols="12" class="mt-2">
                    <b-button class="mm-modal-market-button-alt w-50" @click="selectMarket(market)">
                        {{ market.title }}
                    </b-button>
                </b-col>
            </b-row>  
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'MarketSelection',
        props:{
            'callback': {
                type: Function
            },
            'data': {
                type: Object
            }
        },
        data() {
            return {
            };
        },
        methods: {
            selectMarket(market) {
                this.data.index_market_object.market = market;
                this.callback(market);
            },
        },
        mounted() {
            
        }
    }
</script>