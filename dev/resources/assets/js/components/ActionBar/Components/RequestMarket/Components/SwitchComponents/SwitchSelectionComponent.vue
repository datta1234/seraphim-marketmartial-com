<template>
    <div dusk="switch-selection" class="switch-selection">
        <b-container fluid>
            <b-row :key="index" v-for="(switch_option, index) in switch_options" align-h="center">
                <b-col cols="12">
                    <b-row align-h="center">
                        <b-col cols="3">
                            <label :for="'option-'+index+'-choice'">Select Options:</label>
                        </b-col>
                        <b-col cols="9">
                            <b-form-radio-group :id="'option-'+index+'-choice'"
                                                v-model="switch_options[index].is_index"
                                                :options="options"
                                                :name="'option-'+index+'-choice'"
                                                @change="toggleOption($event,index)">
                            </b-form-radio-group>
                        </b-col>
                    </b-row>
                    <b-row v-if="switch_option.is_index" align-h="center">
                        <b-col cols="3">
                            <label class="mr-sm-2" :for="'index-choice-'+index">Underlying Name:</label>
                        </b-col>
                        <b-col cols="9">
                            <b-form-select :id="'index-choice-'+index"
                                           class="w-50"
                                           :options="index_options"
                                           v-model="switch_option.index_market_id">
                            </b-form-select>
                        </b-col>
                    </b-row>
                    <b-row v-else align-h="center">
                        <b-col cols="3">
                            <label class="mr-sm-2" for="admin-filter-paid">Underlying Name:</label>
                        </b-col>
                        <b-col cols="9">
                            <type-head-input-component  route="/trade/stock" 
                                                        :callback="(stock, is_listed) => setSelectedStock(stock, is_listed, index)" 
                                                        class="w-50">
                            </type-head-input-component>
                            <p  v-if="stockFound(index)" 
                                class="modal-warning-text text-danger text-center w-50">
                                *Warning: Stock not found.
                            </p>
                        </b-col>
                    </b-row>
                    <b-row v-if="index == 0" class="mt-4 mb-4" align-h="center">
                        <b-col cols="12" class="text-center">
                            <h2>VS.</h2>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>            

            <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                <b-col v-for="(error, index) in errors.messages" :key="index" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
            <b-form-group class="text-center mt-4 mb-0">
                <b-button :disabled="false" 
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
    import TypeHeadInputComponent from '../TypeHeadInputComponent.vue';

    export default {
        name: 'SwitchSelection',
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
        data() {
            return {
                options: [
                    { text: 'Index Options', value: true },
                    { text: 'Single Stock Options', value: false },
                ],
                index_options: [],
                chosen_option: null,
                switch_options: [
                    {
                        is_index: true,
                        stock_listed: false,
                        stock_selection: null,
                        index_market_id: null,
                    },
                    {
                        is_index: false,
                        stock_listed: false,
                        stock_selection: null,
                        index_market_id: null,
                    },  
                ], 
                stock_selection: null,
                stock_listed: false,
            };
        },
        methods: {
            /**
             * Sets the selected stock and calls the component callback method
             */
            selectStock() {
                console.log("Options: ",this.switch_options);
                let is_complete = false;

                this.switch_options.forEach(switch_option => {
                    is_complete = (switch_option.is_index && switch_option.index_market_id) 
                        || (!switch_option.is_index && switch_option.stock_selection)
                });
                if( is_complete ) {
                    // @TODO - Complete title and add to call back
                    // Underlying vs. Underlying
                    console.log("YEEET");
                    //this.callback(,this.switch_options);
                }
            },
            setSelectedStock(stock, is_listed, index) {
                this.switch_options[index].stock_selection = stock;
                this.switch_options[index].stock_listed = is_listed;
            },
            stockFound(index) {
                return this.switch_options[index].stock_selection ?
                    this.switch_options[index].stock_selection.code.length > 0 
                        && !this.switch_options[index].stock_listed 
                    : false;
            },
            loadIndexMarkets() {
                this.data.market_object.index_markets.forEach( index_market => {
                    this.index_options.push({ text: index_market.title, value: index_market.id });
                });
                /*this.switch_options[0].index_market_id = this.index_options[0].value;
                this.switch_options[1].index_market_id = this.index_options[0].value;*/
            },
            toggleOption(option, index) {
                if(option) {
                    this.switch_options[index].stock_listed = false;
                    this.switch_options[index].stock_selection = null;
                } else {
                    this.switch_options[index].index_market_id = null;
                }
            }
        },
        mounted() {
            this.loadIndexMarkets();
        }
    }
</script>