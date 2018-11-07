<template>
    <div dusk="switch-selection" class="switch-selection">
        <b-container fluid>
            <mm-loader theme="light" :default_state="true" event_name="requestMarketsLoaded" width="200" height="200"></mm-loader>
            <b-row v-if="markets_loaded" :key="index" v-for="(switch_option, index) in switch_options" align-h="center">
                <b-col cols="12">
                    <b-row v-if="data.market_types.length > 1" align-h="center">
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
                                           v-model="switch_option.index_market">
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
                <b-button :disabled="canProceed" 
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
    import { EventBus } from '~/lib/EventBus.js';
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
        computed: {
            'canProceed': function(){
                return !this.switch_options.reduce( (accumulator, currentValue) => {
                    if(currentValue.is_index) {
                        return accumulator && ( currentValue.index_market ? true : false);
                    } else {
                        return accumulator && ( currentValue.stock_selection ? true : false);
                    }
                }, true);
            }
        },
        data() {
            return {
                options: [
                    { text: 'Index Options', value: true },
                    { text: 'Single Stock Options', value: false },
                ],
                index_options: [{ text: "Select an Index Option", value: null }],
                switch_options: [], 
                stock_selection: null,
                stock_listed: false,
                markets_loaded: false,
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
                    is_complete = (switch_option.is_index && switch_option.index_market) 
                        || (!switch_option.is_index && switch_option.stock_selection)
                });
                if( is_complete ) {
                    this.callback(this.getTitle(),this.switch_options);
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
            /**
             * Loads Markets from API
             */
            loadIndexMarkets() {
                axios.get(axios.defaults.baseUrl + '/trade/market-type/'+this.data.market_types[0].id+'/market')
                .then(marketsResponse => {
                    if(marketsResponse.status == 200) {
                        marketsResponse.data.forEach(market => {
                            this.index_options.push({ text: market.title, value: market });
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
            toggleOption(option, index) {
                if(option) {
                    this.switch_options[index].stock_listed = false;
                    this.switch_options[index].stock_selection = null;
                } else {
                    this.switch_options[index].index_market = null;
                }
            },
            getTitle() {
                let title = [];
                // Gets the selected Underlying
                this.switch_options.forEach(switch_option => {
                    if(switch_option.is_index && switch_option.index_market) {
                        title.push(switch_option.index_market.title);
                    } else if(!switch_option.is_index && switch_option.stock_selection) {
                        title.push(switch_option.stock_selection.code);
                    }
                });
                // Joins the Selected Underlying into title with format: Underlying vs. Underlying
                return title.join(' vs. ');
            }
        },
        mounted() {
            let options = {};
            this.data.market_types.forEach( element => {
                switch(element.title) {
                    case 'Index Option':
                        options.is_index = false;
                        options.index_market = null;
                        break;
                    case 'Single Stock Options':
                        options.stock_listed = false;
                        options.stock_selection = null;
                        break;
                }
            });
            this.switch_options.push(Object.assign({}, options));
            this.switch_options.push(Object.assign({}, options));

            if(this.data.market_types.length > 1) {
                this.switch_options[0].is_index = true;
            } else {
                this.switch_options.forEach(switch_option => {
                    switch_option.is_index = true;
                });
            }
            this.markets_loaded = false,
            this.loadIndexMarkets();
        }
    }
</script>