<template>
    <div dusk="stock-selection" class="stock-selection">
        <b-container fluid>
            <b-row class="text-center">
                <b-col class="mt-0 text-center" cols="12">
                    <h4 class="modal-info-text">Stock Name:</h4>
                </b-col>
                <b-col class="mt-0 text-center" cols="12">
                    <p class="modal-info-text">
                        Start typing the stock ticker required. Results will be presented automatically
                    </p>
                </b-col>
                <b-col cols="12" class="mt-2">
                    <vue-bootstrap-typeahead v-model="stock" :data="stocks"/>
                </b-col>
            </b-row>
            <!-- <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                <b-col v-for="(error, index) in errors.messages" :key="index" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row> -->
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'StockSelection',
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
                stock: '',
                stocks: ['Canada', 'USA', 'Mexico'],
            };
        },
        methods: {
            /**
             * Sets the selected stock and calls the component callback method
             */
            selectMarket(market) {
                this.data.index_market_object.market = market;
                this.callback(market.title);
            },
        },
        mounted() {

        }
    }
</script>