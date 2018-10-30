<template>
    <div dusk="stock-selection" class="stock-selection">
        <b-container fluid>
            <b-row class="justify-content-md-center">
                <b-col class="mt-0 text-center" cols="12">
                    <h4>Stock Name:</h4>
                </b-col>
                <b-col class="mt-0 text-center" cols="12">
                    <p class="modal-info-text">
                        Start typing the stock ticker required. Results will be presented automatically
                    </p>
                </b-col>
            </b-row>
            <b-row class="justify-content-md-center">
                <b-col cols="6" class="mt-2">
                    <type-head-input-component route="/trade/stock" :callback="setSelectedStock">
                    </type-head-input-component>
                    <p v-if="stockFound" class="modal-warning-text text-danger text-center">
                        *Warning: Stock not found.
                    </p>
                </b-col>
            </b-row>
            <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                <b-col v-for="(error, index) in errors.messages" :key="index" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
            <b-form-group class="text-center mt-4 mb-0">
                <b-button :disabled="!this.stock_selection" 
                          id="submit-index-details" 
                          class="mm-modal-market-button-alt w-25" 
                          @click="selectStock()">
                    Next
                </b-button>
            </b-form-group>
        </b-container>
    </div>
</template>

<script>
    import TypeHeadInputComponent from './TypeHeadInputComponent.vue';

    export default {
        name: 'StockSelection',
        components: {
            TypeHeadInputComponent,
        },
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
        computed: {
            'stockFound': function(){
                return this.stock_selection ?
                    this.stock_selection.code.length > 0 && !this.stock_listed :
                    false;
            }
        },
        data() {
            return {
                stock_selection: null,
                stock_listed: false,
            };
        },
        methods: {
            /**
             * Sets the selected stock and calls the component callback method
             */
            selectStock() {
                if(this.stock_selection) {
                    this.callback(this.stock_selection.code,this.stock_selection);
                }
            },
            setSelectedStock(stock, is_listed) {
                this.stock_selection = stock;
                this.stock_listed = is_listed;
            },
        },
        mounted() {

        }
    }
</script>