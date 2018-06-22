<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="justify-content-md-center">
                <b-col v-if="data" v-for="market in data.market_type.markets" cols="6" class="mt-2">
                    <b-button class="mm-modal-market-button w-100" @click="selectMarket(market)">
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
                this.callback(market);
            },
        },
        mounted() {
            
        }
    }
</script>