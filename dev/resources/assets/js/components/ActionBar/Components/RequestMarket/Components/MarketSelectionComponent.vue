<template>
    <div dusk="market-selection" class="step-selections">
        <b-container fluid>
            <b-row class="text-center">
                <b-col v-if="data" v-for="(market, index) in data.market_type.markets" :key="index" cols="12" class="mt-2">
                    <b-button :id="market.title+'-market-choice'" class="mm-modal-market-button-alt w-50" @click="selectMarket(market)">
                        {{ market.title }}
                    </b-button>
                </b-col>
            </b-row>
            <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                <b-col v-for="(error, index) in errors.messages" :key="index" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
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
            },
            'errors': {
                type: Object
            }
        },
        data() {
            return {
            };
        },
        methods: {
            /**
             * Sets the selected market and calls the component call back method
             */
            selectMarket(market) {
                /*this.data.index_market_object.market = market;*/
                this.callback(market.title,market);
            },
        },
        mounted() {

        }
    }
</script>