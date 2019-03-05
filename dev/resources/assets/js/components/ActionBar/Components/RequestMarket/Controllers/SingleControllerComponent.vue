<template>
    <div dusk="single-controller" class="single-controller">
        <mm-loader theme="light" :default_state="false" event_name="requestSubmissionLoaded" width="200" height="200"></mm-loader>
        <component v-if="submitting_request" v-bind:is="components[selected_step_component]" :errors="errors.data[selected_step_component]" :data="stock_data" :callback="loadStepComponent"></component>
    </div>
</template>

<script>
    import StockSelection from '../Components/StockSelectionComponent.vue';
    import StructureSelection from '../Components/StructureSelectionComponent.vue';
    import ExpirySelection from '../Components/ExpirySelectionComponent.vue';
    import Details from '../Components/DetailsComponent.vue';
    import ConfirmMarketRequest from '../Components/ConfirmMarketRequestComponent.vue';

    import { EventBus } from '~/lib/EventBus.js';
    export default {
        name: 'SingleController',
        props:{
            'close_modal': {
                type: Function
            },
            'callback': {
                type: Function
            },
            'modal_data': {
                type: Object
            }
        },
        data() {
            return {
                stock_data: {
                    market_type_title:'Single Stock Options',
                    market_type: null,
                    market_object: {
                        stock: null,
                        stock_code: null,
                        //market:null,
                        trade_structure: '',
                        trade_structure_groups: [],
                        expiry_dates:[],
                        details: null,

                    },
                    number_of_dates: 1,
                },
                errors: {
                    message: null,
                    data: {
                        Stock:{
                            messages:[]
                        },
                        Structure:{
                            messages:[]
                        },
                        Expiry:{
                            messages:[]
                        },
                        Details:{
                            messages:[],
                            fields:[]
                        },
                        Confirm:{
                            messages:[]
                        },
                    },
                },
                selected_step_component: null,
                components: {
                    Stock: StockSelection,
                    Structure: StructureSelection,
                    Expiry: ExpirySelection,
                    Confirm: ConfirmMarketRequest,
                    Details: Details,
                },
                temp_title: [],
                submitting_request: true,
            };
        },
        methods: {
            /**
             * Returns to previous modal step 
             */
            previousStep() {
                this.modal_data.title.pop();
                this.modal_data.step--;
            },
            /**
             * Goes to next modal step 
             */
            nextStep() {
                this.modal_data.step++;
            },
            /**
             * Loads step component 
             */
            loadStepComponent(step_detail, component_data) {
                if( step_detail != 'back' ) {
                    this.nextStep();
                    if(step_detail) {
                        this.modal_data.title.push(step_detail);
                    }
                } else {
                    if (this.modal_data.title[0] =='Confirm Market Request') {
                        this.modal_data.title = this.temp_title;
                    } else {
                        this.modal_data.title.pop();
                    }
                }
                switch (this.modal_data.step) {
                    case 2:
                        this.selected_step_component = 'Stock';
                        break;
                    case 3:
                        this.stock_data.market_object.stock = component_data ? 
                            component_data : this.stock_data.market_object.stock;
                        this.selected_step_component = 'Structure';
                        break;
                    case 4:
                        this.stock_data.market_object.trade_structure = component_data ? 
                            component_data : this.stock_data.market_object.trade_structure;
                        this.stock_data.number_of_dates = 1;
                        if (this.stock_data.market_object.trade_structure == 'Calendar') {
                            this.stock_data.number_of_dates = 2;
                        }
                        this.selected_step_component = 'Expiry';                   
                        break;
                    case 5:
                        this.stock_data.market_object.expiry_dates = component_data ?
                            component_data : this.stock_data.market_object.expiry_dates;
                        this.selected_step_component = 'Details';
                        break;
                    case 6:
                        this.stock_data.market_object.details = component_data ?
                            component_data : this.stock_data.market_object.details;
                        this.temp_title = this.modal_data.title;
                        this.modal_data.title = ['Confirm Market Request'];
                        this.selected_step_component = 'Confirm';
                        break;
                    case 7:
                        this.saveMarketRequest();
                    default:
                }
            },
            /**
             * Loads Singles MarketType
             */
            loadMarketType() {
                if(Array.isArray(this.$root.market_types)) {
                    this.$root.market_types.forEach((element) => {
                        if(element.title == this.stock_data.market_type_title) {
                            this.stock_data.market_type = element;
                        }
                    });
                }
            },
            /**
             * Loads Markets from API
             */
            loadMarkets() {
                axios.get(axios.defaults.baseUrl + '/trade/market-type/'+this.stock_data.market_type.id+'/market')
                .then(marketsResponse => {
                    if(marketsResponse.status == 200) {
                        this.stock_data.market_object.market = marketsResponse.data[0];
                    } else {
                        //console.error(err);    
                    }
                }, err => {
                    //console.error(err);
                });
            },
            /**
             * Saves Market Request with the completed composed data and then closes the modal
             */
            saveMarketRequest() {
                // toggle loading
                EventBus.$emit('loading', 'requestSubmission');
                this.submitting_request = false;
                
                let new_data = this.formatRequestData();
                axios.post(axios.defaults.baseUrl + '/trade/market/'+ this.stock_data.market_object.market.id +'/market-request', new_data)
                .then(newMarketRequestResponse => {
                    // success closes the modal
                    this.close_modal();
                    
                    // toggle loading
                    EventBus.$emit('loading', 'requestSubmission');
                    this.submitting_request = true;

                }).catch( (err) => {
                    // toggle loading
                    EventBus.$emit('loading', 'requestSubmission');
                    this.submitting_request = true;
                    this.setLowestStep(5);
                    // server error loads the response errors and loads error step
                    if (err.response && err.response.data.message) {
                        // The request was made and the server responded with a status code
                        // that falls out of the range of 2xx
                        this.errors.message = err.response.data.message;
                        this.loadErrorStep(err.response.data.errors);
                    } else if (err.request) {
                      // The request was made but no response was received
                      // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                      // http.ClientRequest in node.js
                      //console.error(err.request);
                      this.$toasted.error("a Server connection error has occurred.");
                    } else {
                      // Something happened in setting up the request that triggered an Error
                      //console.error(err.message);
                      this.$toasted.error("Oops, Something went wrong.");
                    }
                    //console.error(err.config);
                    this.loadStepComponent();
                });
            },
            /**
             * Formats the component data to a format to submit to the api for a new Single Stock Market
             *
             * @return {Object} - formatted data object
             */
            formatRequestData() {
                // sets initial object structure
                let formatted_data = {
                    trade_structure: this.stock_data.market_object.trade_structure,
                    trade_structure_groups:[]
                }
                this.stock_data.market_object.details.fields.forEach( (element,key) => {
                    let group_data = {
                        is_selected: element.is_selected,
                        stock: this.stock_data.market_object.stock.code,
                        fields: {
                            "Expiration Date": this.castToMoment( (formatted_data.trade_structure == 'Calendar') ? 
                                this.stock_data.market_object.expiry_dates[key]
                                : this.stock_data.market_object.expiry_dates[0] ),                            
                            Quantity: element.quantity,
                            Strike: element.strike,
                        }
                    };

                    if(element.has_future) {
                        group_data.fields["Future"] = element.future;
                    }

                    formatted_data.trade_structure_groups.push(group_data);
                });
                return formatted_data;
            },
            /**
             * Loads the earliest step correlating to the passed errors and adds the errors
             *  to the correct component error in this.errors.data
             *
             * @params {Object} errors
             */
            loadErrorStep(errors) {
                for(let prop in errors) {
                    if(prop.indexOf('.') != -1) {
                        let propArr = prop.split('.');
                        switch (propArr[2]) {
                            case "stock":
                                errors[prop].forEach( (element, key) => {
                                    if (this.errors.data.Stock.messages.indexOf(element) == -1) {
                                        this.errors.data.Stock.messages.push(element);
                                    }
                                });
                                this.temp_title.splice(-3);
                                this.setLowestStep(1);
                                break;
                            case "is_selected":
                                errors[prop].forEach( (element, key) => {
                                    if (this.errors.data.Details.messages.indexOf(element) == -1) {
                                        this.errors.data.Details.messages.push(element);
                                    }
                                });
                                this.setLowestStep(4);
                                break;
                            case "fields":
                                if(propArr[3] == 'Expiration Date') {
                                    errors[prop].forEach( (element, key) => {
                                        if (this.errors.data.Expiry.messages.indexOf(element) == -1) {
                                            this.errors.data.Expiry.messages.push(element);
                                        }
                                    });
                                    this.temp_title.splice(-1);
                                    this.setLowestStep(3);
                                } else {
                                    errors[prop].forEach( (element, key) => {
                                        if (this.errors.data.Details.messages.indexOf(element) == -1) {
                                            this.errors.data.Details.messages.push(element);
                                        }
                                    });
                                    if (this.errors.data.Details.fields.indexOf(prop) == -1) {
                                        this.errors.data.Details.fields.push(prop);
                                    }
                                    this.setLowestStep(4);
                                }
                                break;
                            default:
                                errors[prop].forEach( (element, key) => {
                                    if (this.errors.data.Confirm.messages.indexOf(element) == -1) {
                                        this.errors.data.Confirm.messages.push(element);
                                    }
                                });
                        }
                    } else {
                        if (prop.indexOf('trade_structure_groups') != -1) {
                            errors[prop].forEach( (element, key) => {
                                if (this.errors.data.Details.messages.indexOf(element) == -1) {
                                    this.errors.data.Details.messages.push(element);
                                }
                            });
                            this.setLowestStep(4);
                        } else {
                            errors[prop].forEach( (element, key) => {
                                if (this.errors.data.Structure.messages.indexOf(element) == -1) {
                                    this.errors.data.Structure.messages.push(element);
                                }
                            });
                            this.temp_title.splice(-2);
                            this.setLowestStep(2);
                        }
                    }
                }
                if(this.errors.message != null) {
                    this.$toasted.error(this.errors.message);
                }
                this.modal_data.title = this.modal_data.step > 4 ? this.modal_data.title : this.temp_title;
            },
            /**
             * Sets a new lowest step if the passed step is lower than the current lowest step
             */
            setLowestStep(new_step) {
                if(new_step < this.modal_data.step) {    
                    this.modal_data.step = new_step;
                }
            },
            /**
             * Casting a passed string to moment with a new format
             *
             * @param {string} date_string
             */
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
            },
        },
        mounted() {
            this.modal_data.title = ["Stock"];
            this.loadMarketType();
            this.loadMarkets();
            this.selected_step_component = 'Stock';
            this.$on('modal_step', this.loadStepComponent);
            this.submitting_request = true;
        }
    }
</script>
