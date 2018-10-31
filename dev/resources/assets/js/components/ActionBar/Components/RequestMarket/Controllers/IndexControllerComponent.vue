<template>
    <div dusk="index-controller" class="index-controller">
        <mm-loader theme="light" :default_state="false" event_name="requestSubmissionLoaded" width="200" height="200"></mm-loader>
        <component v-if="submitting_request" v-bind:is="components[selected_step_component]" :errors="errors.data[selected_step_component]" :data="index_data" :callback="loadStepComponent"></component>
    </div>
</template>

<script>
    import MarketSelection from '../Components/MarketSelectionComponent.vue';
    import StructureSelection from '../Components/StructureSelectionComponent.vue';
    import ExpirySelection from '../Components/ExpirySelectionComponent.vue';
    import Details from '../Components/DetailsComponent.vue';
    import ConfirmMarketRequest from '../Components/ConfirmMarketRequestComponent.vue';

    import Market from '../../../../../lib/Market';
    import { EventBus } from '../../../../../lib/EventBus.js';
    export default {
        name: 'IndexController',
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
                index_data: {
                    market_type_title:'Index Option',
                    market_type: null,
                    market_object: {

                        market:null,
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
                        Market:{
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
                    Market: MarketSelection,
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
            loadStepComponent(step_detail,component_data) {
                if( step_detail != 'back' ) {
                    this.nextStep();
                    if(step_detail) {
                        this.modal_data.title.push(step_detail);
                    }
                } else {
                    if (this.modal_data.title[0] =='Confirm Market Request') {
                        this.modal_data.title = this.temp_title;
                    }
                    this.modal_data.title.pop();
                }
                switch (this.modal_data.step) {
                    case 2:
                        this.selected_step_component = 'Market';
                        break;
                    case 3:
                        this.index_data.market_object.market = component_data ? 
                            component_data : this.index_data.market_object.market;
                        this.selected_step_component = 'Structure';
                        break;
                    case 4:
                        this.index_data.market_object.trade_structure = component_data ? 
                            component_data : this.index_data.market_object.trade_structure;
                        this.index_data.number_of_dates = 1;
                        if (this.index_data.market_object.trade_structure == 'Calendar') {
                            this.index_data.number_of_dates = 2;
                        }
                        this.selected_step_component = 'Expiry';                   
                        break;
                    case 5:
                        this.index_data.market_object.expiry_dates = component_data ?
                            component_data : this.index_data.market_object.expiry_dates;
                        this.selected_step_component = 'Details';
                        break;
                    case 6:
                        this.index_data.market_object.details = component_data ?
                            component_data : this.index_data.market_object.details;
                        this.temp_title = this.modal_data.title;
                        this.modal_data.title = ['Confirm Market Request'];
                        this.selected_step_component = 'Confirm';
                        break;
                    case 7:
                        this.saveMarketRequest();
                    default:
                }
                console.log("Current Data: ", this.index_data);
            },
            /**
             * Loads Index Markets 
             */
            loadIndexMarkets() {
                if(Array.isArray(this.$root.market_types)) {
                    this.$root.market_types.forEach((element) => {
                        if(element.title == this.index_data.market_type_title) {
                            this.index_data.market_type = element;
                        }
                    });
                }
            },
            /**
             * Saves Market Request with the completed composed data and then closes the modal
             */
            saveMarketRequest() {
                // toggle loading
                EventBus.$emit('loading', 'requestSubmission');
                this.submitting_request = false;
                
                let new_data = this.formatRequestData();
                axios.post(axios.defaults.baseUrl + '/trade/market/'+ this.index_data.market_object.market.id +'/market-request', new_data)
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
                      console.error(err.request);
                    } else {
                      // Something happened in setting up the request that triggered an Error
                      console.error(err.message);
                    }
                    console.error(err.config);
                });
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
                            case "market_id":
                                errors[prop].forEach( (element, key) => {
                                    if (this.errors.data.Market.messages.indexOf(element) == -1) {
                                        this.errors.data.Market.messages.push(element);
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
                this.loadStepComponent();
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
             * Formats the component data to a format to submit to the api for a new Index Market
             *
             * @return {Object} - formatted data object
             */
            formatRequestData() {
                // sets initial object structure
                let formatted_data = {
                    trade_structure: this.index_data.market_object.trade_structure,
                    trade_structure_groups:[]
                }
                this.index_data.market_object.details.fields.forEach( (element,key) => {
                    formatted_data.trade_structure_groups.push({
                        is_selected: element.is_selected,
                        market_id: this.index_data.market_object.market.id,
                        fields: {
                            "Expiration Date": this.castToMoment( (formatted_data.trade_structure == 'Calendar') ? this.index_data.market_object.expiry_dates[key] : this.index_data.market_object.expiry_dates[0] ),
                            Strike: element.strike,
                            Quantity: element.quantity
                        }
                    });
                });
                return formatted_data;
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
            this.modal_data.title = ["Index"];
            this.loadIndexMarkets();
            this.selected_step_component = 'Market';
            this.$on('modal_step', this.loadStepComponent);
            this.submitting_request = true;
        }
    }
</script>
