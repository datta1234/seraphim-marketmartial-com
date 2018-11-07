<template>
    <div dusk="market-selection" class="step-selections">
        <b-container fluid>
            <mm-loader theme="light" :default_state="true" event_name="requestMarketsLoaded" width="200" height="200"></mm-loader>
            <b-row class="text-center">
                <b-col v-if="markets_loaded && markets.length > 0" v-for="(market, index) in markets" :key="index" cols="12" class="mt-2">
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
    import { EventBus } from '~/lib/EventBus.js';
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
                markets: [],
                markets_loaded: false,
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
            /**
             * Loads Markets from API
             */
            loadMarkets() {
                axios.get(axios.defaults.baseUrl + '/trade/market-type/'+this.data.market_type.id+'/market')
                .then(marketsResponse => {
                    if(marketsResponse.status == 200) {
                        marketsResponse.data.forEach(market => {
                            this.markets.push(market);
                        });
                        EventBus.$emit('loading', 'requestMarkets');
                        this.markets_loaded = true;
                    } else {
                        this.$toasted.error("Failed to load markets");
                        console.error(err);    
                    }
                }, err => {
                    this.$toasted.error("Failed to load markets");
                    console.error(err);
                });
            },
        },
        mounted() {
            this.markets_loaded = false;
            this.loadMarkets();
        }
    }
</script>