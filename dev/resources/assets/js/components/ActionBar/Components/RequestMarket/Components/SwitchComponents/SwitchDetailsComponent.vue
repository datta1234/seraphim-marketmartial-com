<template>
    <div dusk="switch-details" class="switch-details">
        <b-container fluid>
            <mm-loader theme="light" :default_state="true" event_name="requestDatesLoaded" width="200" height="200"></mm-loader>
            <b-row v-if="dates_loaded" class="justify-content-md-center">
            	<b-col cols="12">
	                <b-form @submit="submitDetails" id="index-details-form">
                        <!-- Title section -->
						<b-row class="mt-2">
                            <b-col cols="5" class="text-center">
                                <b-row>
                                    <b-col cols="6" offset="3">
                                        <h4>
                                            {{ data.market_object.switch_options[0].is_index ?
                                                data.market_object.switch_options[0].index_market.title
                                                : data.market_object.switch_options[0].stock_selection.code }}
                                        </h4>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="2" class="text-center">
                                <h4>VS.</h4>
                            </b-col>
                            <b-col cols="5" class="text-center">
                                <b-row>
                                    <b-col cols="6" offset="3">
                                        <h4>
                                            {{ data.market_object.switch_options[1].is_index ?
                                                data.market_object.switch_options[1].index_market.title
                                                : data.market_object.switch_options[1].stock_selection.code }}
                                        </h4>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                        <!-- Expiry section -->
                        <b-row class="mt-2">
                            <b-col cols="5">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="mr-sm-2 pt-2" for="option-expiry-0">Expiry</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-select id="option-expiry-1"
                                                       :options="expiry_dates"
                                                       :state="inputState(0, 'Expiration Date')"
                                                       v-model="form_data.fields[0].expiration"
                                                       required>
                                        </b-form-select>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="5" offset="2">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="mr-sm-2 pt-2" for="option-expiry-1">Expiry</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-select id="option-expiry-2"
                                                       :options="expiry_dates"
                                                       :state="inputState(1, 'Expiration Date')"
                                                       v-model="form_data.fields[1].expiration"
                                                       required>
                                        </b-form-select>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                        <!-- Choice section -->
                        <b-row>
                            <b-col cols="12">
                                <b-form-radio-group id="risky-choices" 
                                                    v-model="chosen_option" 
                                                    name="choice"
                                                    class="mb-2 mt-4">
                                    <b-row>
                                        <b-col cols="5">
                                            <b-row>
                                                <b-col cols="6" offset="2">
                                                    <b-form-radio class="ml-5" id="choice-0" value="0">CHOICE</b-form-radio>
                                                </b-col>
                                            </b-row> 
                                        </b-col>
                                        <b-col cols="5" offset="2">
                                            <b-row>
                                                <b-col cols="6" offset="2">
                                                    <b-form-radio class="ml-5" id="choice-1" value="1">CHOICE</b-form-radio>    
                                                </b-col>
                                            </b-row>
                                        </b-col>
                                    </b-row>
                                </b-form-radio-group>
                            </b-col>
                        </b-row>
                        <!-- Strike section -->
                        <b-row>
                            <b-col cols="5">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="pt-2" for="strike-0">Strike</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-input id="strike-0"
                                            v-model="form_data.fields[0].strike"
                                            :state="inputState(0, 'Strike')"
                                            required>
                                        </b-form-input>
                                    </b-col>
                                    <b-col cols="3">
                                        <label class="pt-2" for="strike-0">
                                            {{ form_data.fields[0].is_index ? "Points" : "ZAR"}}
                                        </label>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="5" offset="2">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="pt-2" for="strike-1">Strike</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-input id="strike-1"
                                            v-model="form_data.fields[1].strike"
                                            :state="inputState(1, 'Strike')"
                                            required>
                                        </b-form-input>
                                    </b-col>
                                    <b-col cols="3">
                                        <label class="pt-2" for="strike-1">
                                            {{ form_data.fields[1].is_index ? "Points" : "ZAR"}}
                                        </label>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                        <!-- Quantity section -->
                        <b-row>
                            <b-col cols="5">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="pt-2" for="quantity-0">Quantity</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-input id="quantity-0" 
                                            type="number"
                                            min="0"
                                            v-model="form_data.fields[0].quantity"
                                            :placeholder="form_data.fields[0].quantity_default+''"
                                            :state="inputState(0, 'Quantity')"
                                            required>
                                        </b-form-input>
                                        <p  v-if="form_data.fields[0].quantity < form_data.fields[0].quantity_default"
                                            class="modal-warning-text text-danger text-center">
                                            *Warning: The recommended minimum quantity is {{ form_data.fields[0].quantity_default }}.
                                        </p>
                                    </b-col>
                                    <b-col cols="3">
                                        <label class="pt-2" for="quantity-0">
                                            {{ form_data.fields[0].is_index ? "Contracts" : "Rm"}}
                                        </label>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="5" offset="2">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="pt-2" for="quantity-1">Quantity</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-form-input id="quantity-1" 
                                            type="number"
                                            min="0"
                                            v-model="form_data.fields[1].quantity"
                                            :placeholder="form_data.fields[1].quantity_default+''"
                                            :state="inputState(1, 'Quantity')"
                                            required>
                                        </b-form-input>
                                        <p  v-if="form_data.fields[1].quantity < form_data.fields[1].quantity_default"
                                            class="modal-warning-text text-danger text-center">
                                            *Warning: The recommended minimum quantity is {{ form_data.fields[1].quantity_default }}.
                                        </p>
                                    </b-col>
                                    <b-col cols="3">
                                        <label class="pt-2" for="quantity-1">
                                            {{ form_data.fields[1].is_index ? "Contracts" : "Rm"}}
                                        </label>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                       <!-- Future section -->
                       <b-row class="mt-3">
                            <b-col cols="5">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="pt-2" for="future-0">Future Price Reference</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-input-group>
                                            <b-input-group-prepend is-text class="optional-input-prepend">
                                                <input v-model="form_data.fields[0].has_future" 
                                                       type="checkbox"
                                                       class="optional-input-prepend-checkbox"
                                                       aria-label="Include a future price">
                                            </b-input-group-prepend>
                                            <b-form-input :disabled="!form_data.fields[0].has_future"
                                                          v-model="form_data.fields[0].future"
                                                          placeholder="Optional"
                                                          :state="inputState(0, 'Future')"
                                                          aria-label="Input for optional future price">
                                            </b-form-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col cols="5" offset="2">
                                <b-row>
                                    <b-col cols="3">
                                        <label class="pt-2" for="future-1">Future Price Reference</label>
                                    </b-col>
                                    <b-col cols="6">
                                        <b-input-group>
                                            <b-input-group-prepend is-text class="optional-input-prepend">
                                                <input v-model="form_data.fields[1].has_future" 
                                                       type="checkbox"
                                                       class="optional-input-prepend-checkbox"
                                                       aria-label="Include a future price">
                                            </b-input-group-prepend>
                                            <b-form-input :disabled="!form_data.fields[1].has_future"
                                                          v-model="form_data.fields[1].future"
                                                          placeholder="Optional"
                                                          :state="inputState(1, 'Future')"
                                                          aria-label="Input for optional future price">
                                            </b-form-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>

                        <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                            <b-col :key="index" v-for="(error, index) in errors.messages" cols="12">
                                <p class="text-danger mb-0">{{ error }}</p>
                            </b-col>
                        </b-row>
	                    
	                    <b-form-group class="text-center mt-4 mb-0">
	                        <b-button id="submit-index-details" type="submit" class="mm-modal-market-button-alt w-50">
	                            Submit
	                        </b-button>
	                    </b-form-group>
	                </b-form>
            	</b-col>
            </b-row>  
        </b-container>
    </div>
</template>

<script>
    import { EventBus } from '~/lib/EventBus.js';
    export default {
        name: 'SwitchDetails',
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
        watch: {
            'chosen_option': function(chosen_index) {
                this.form_data.fields.forEach( (element, index) => {
                    element.is_selected = (chosen_index == index) ? true : false;
                });
            }
        },
        data() {
            return {
            	display:{
            		show_expiry: false,
            		disable_choice: false,
            	},
            	chosen_option: null,
                form_data: {
                	fields: []
                },
                dates_loaded: false,
                expiry_dates: [
                    {text: "Select Expiry", value: null}
                ],
                quantity_default: {
                    TOP40: 500,
                    DTOP: 2500,
                    DCAP: 1500,
                    stock: 50,
                }
            };
        },
        methods: {
            /**
             * Sets the form data to the passed data object and calls the
             *  component call back method.
             *
             * @param {Event} evt - form submit event
             */
            submitDetails(evt) {
                evt.preventDefault();
                Vue.nextTick( () => {
                    /*this.data.market_object.details = this.form_data;*/
                	this.callback('',this.form_data);
				})
            },
            /**
             * Casting a passed string to moment with a new format
             *
             * @param {string} date_string
             */
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('MMMYY');
            },
            /**
             * Toggles input states when there are errors for the input
             *
             * @param {number} index - the index of the input
             * @param {string} type - the type of input
             */
            inputState(index, type) {
                return (this.errors.fields.indexOf('trade_structure_groups.'+ index +'.fields.'+ type) == -1)? null: false;
            },
            /**
             * Loads Expiry Dates from API and sets pagination variables
             */
            loadExpiryDate() {
                axios.get(axios.defaults.baseUrl + '/trade/safex-expiration-date?page='+this.current_page,  {
                    params:{
                        'not_paginate': true,
                    }
                })
                .then(expiryDateResponse => {
                    Object.keys(expiryDateResponse.data.data).forEach(key => {
                        let date = moment(expiryDateResponse.data.data[key].date, 'YYYY-MM-DD HH:mm:ss');
                        if(date.isAfter(moment())) {
                            this.expiry_dates.push({
                                text: date.format('MMMYY'),
                                value: expiryDateResponse.data.data[key].date,
                            });
                        }
                    });
                    EventBus.$emit('loading', 'requestDates');
                    this.dates_loaded = true;
                }, err => {
                    console.error(err);
                    this.$toasted.error("Failed to load safex expiration dates");
                });
            },
            setPreviousData() {
                this.data.market_object.switch_options.forEach( (switch_option,index) => {
                    Object.keys(switch_option).forEach(element => {
                        if(this.form_data.fields[index].hasOwnProperty(element)){
                            this.form_data.fields[index][element] = switch_option[element];
                            this.chosen_option = (element == 'is_selected' && switch_option[element])? index : this.chosen_option;
                        }
                    });
                });
            }
        },
        mounted() {
            this.dates_loaded = false;
            this.loadExpiryDate();
            // Sets up the view and object data defaults
            this.data.market_object.switch_options.forEach( (element, index) => {
                let default_size = element.is_index ? this.quantity_default[element.index_market.title]
                    : this.quantity_default.stock;
                this.form_data.fields.push({
                    is_index: element.is_index,
                    expiration:null,
                    is_selected: index == 0 ? true : false,
                    strike: null,
                    quantity: default_size,
                    quantity_default: default_size,
                    future: null,
                    has_future: false,
                });
            });
            this.chosen_option = 0;
            this.setPreviousData();
        }
    }
</script>