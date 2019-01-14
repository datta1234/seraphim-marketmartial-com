<template>
    <div dusk="efp-controller" class="efp-controller">
        <mm-loader theme="light" :default_state="false" event_name="requestSubmissionLoaded" width="200" height="200"></mm-loader>
        <component v-if="submitting_request" v-bind:is="components[selected_step_component]" :errors="errors.data[selected_step_component]" :data="controller_data" :callback="loadStepComponent"></component>
    </div>
</template>

<script>
    import MarketSelection from '../Components/MarketSelectionComponent.vue';
    import ExpirySelection from '../Components/ExpirySelectionComponent.vue';
    import Details from '../Components/DetailsComponent.vue';
    import ConfirmMarketRequest from '../Components/ConfirmMarketRequestComponent.vue';

    import { EventBus } from '~/lib/EventBus.js';
    export default {
        name: 'EFPController',
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
                controller_data: {
                    market_type_title:'Index Option',
                    market_type: null,
                    market_object: {

                        market:null,
                        trade_structure: 'EFP',
                        trade_structure_groups: [],
                        expiry_dates: [],
                        details: null,
                    },
                    number_of_dates: 1,
                    request_market_id: 5,
                },
                errors: {
                    message: null,
                    data: {
                        Market:{
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
                    } else {
                        this.modal_data.title.pop();
                    }
                }
                switch (this.modal_data.step) {
                    case 2:
                        this.selected_step_component = 'Market';
                        break;
                    case 3:
                        this.controller_data.market_object.market = component_data ? 
                            component_data : this.controller_data.market_object.market;
                        this.controller_data.number_of_dates = 1;
                        this.selected_step_component = 'Expiry';                   
                        break;
                    case 4:
                        this.controller_data.market_object.expiry_dates = component_data ?
                            component_data : this.controller_data.market_object.expiry_dates;
                        this.selected_step_component = 'Details';
                        break;
                    case 5:
                        this.controller_data.market_object.details = component_data ?
                            component_data : this.controller_data.market_object.details;
                        this.temp_title = this.modal_data.title;
                        this.modal_data.title = ['Confirm Market Request'];
                        this.selected_step_component = 'Confirm';
                        break;
                    case 6:
                        this.saveMarketRequest();
                    default:
                }
            },
            /**
             * Loads Index MarketType 
             */
            loadMarketType() {
                if(Array.isArray(this.$root.market_types)) {
                    this.$root.market_types.forEach((market_type) => {
                        if(market_type.title == this.controller_data.market_type_title) {
                            this.controller_data.market_type = market_type;
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
                axios.post(axios.defaults.baseUrl + '/trade/market/'+ this.controller_data.request_market_id +'/market-request', this.formatRequestData())
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
                    this.setLowestStep(4);
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
                      this.$toasted.error("a Server connection error has occurred.");
                    } else {
                      // Something happened in setting up the request that triggered an Error
                      console.error(err.message);
                      this.$toasted.error("Oops, Something went wrong.");
                    }
                    console.error(err.config);
                    this.loadStepComponent();
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
                                this.temp_title.splice(-2);
                                this.setLowestStep(1);
                                break;
                            case "fields":
                                if(propArr[3] == 'Expiration Date') {
                                    errors[prop].forEach( (element, key) => {
                                        if (this.errors.data.Expiry.messages.indexOf(element) == -1) {
                                            this.errors.data.Expiry.messages.push(element);
                                        }
                                    });
                                    this.temp_title.splice(-1);
                                    this.setLowestStep(2);
                                } else {
                                    errors[prop].forEach( (element, key) => {
                                        if (this.errors.data.Details.messages.indexOf(element) == -1) {
                                            this.errors.data.Details.messages.push(element);
                                        }
                                    });
                                    if (this.errors.data.Details.fields.indexOf(prop) == -1) {
                                        this.errors.data.Details.fields.push(prop);
                                    }
                                    this.setLowestStep(3);
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
                        errors[prop].forEach( (element, key) => {
                            if (this.errors.data.Confirm.messages.indexOf(element) == -1) {
                                this.errors.data.Confirm.messages.push(element);
                            }
                        });
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
             * Formats the component data to a format to submit to the api for a new Index Market
             *
             * @return {Object} - formatted data object
             */
            formatRequestData() {
                // sets initial object structure
                let formatted_data = {
                    trade_structure: this.controller_data.market_object.trade_structure,
                    trade_structure_groups:[]
                }
                this.controller_data.market_object.details.fields.forEach( (element,index) => {
                    let group_data = {
                        market_id: this.controller_data.market_object.market.id,
                        fields: {
                            "Expiration Date": this.castToMoment( this.controller_data.market_object.expiry_dates[0] ),
                            Quantity: element.quantity,
                        }
                    };

                    formatted_data.trade_structure_groups.push(group_data);
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
            this.modal_data.title = ["EFP"];
            this.loadMarketType();
            this.selected_step_component = 'Market';
            this.$on('modal_step', this.loadStepComponent);
            this.submitting_request = true;
        }
    }
</script>
