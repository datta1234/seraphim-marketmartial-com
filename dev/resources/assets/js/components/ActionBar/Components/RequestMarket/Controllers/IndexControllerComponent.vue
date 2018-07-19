<template>
    <div dusk="index-controller" class="index-controller">
        <b-row v-if="errors.message != null">
            <b-col cols="12">
                <b-alert show dismissible fade variant="danger">{{ errors.message }}</b-alert>
            </b-col>
        </b-row>
        <component v-bind:is="components[selected_step_component]" :errors="errors.data[selected_step_component]" :data="index_data" :callback="loadStepComponent"></component>
    </div>
</template>

<script>
    import MarketSelection from '../Components/MarketSelectionComponent.vue';
    import StructureSelection from '../Components/StructureSelectionComponent.vue';
    import ExpirySelection from '../Components/ExpirySelectionComponent.vue';
    import Details from '../Components/DetailsComponent.vue';
    import ConfirmMarketRequest from '../Components/ConfirmMarketRequestComponent.vue';

    import Market from '../../../../../lib/Market';

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
                    index_market_object: {

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
            };
        },
        methods: {
            /**
             * Returns to previous modal step 
             */
            previousStep() {
                this.modal_data.step--;
            },
            /**
             * Goes to next modal step 
             */
            nextStep() {
                this.modal_data.step++;
                console.log("STEP=========================: ", this.modal_data.step);
            },
            /**
             * Loads step component 
             */
            loadStepComponent(component_data) {
                if( component_data != 'back' ) {
                    this.nextStep();
                }
                switch (this.modal_data.step) {
                    case 2:
                        this.selected_step_component = 'Market';
                        break;
                    case 3:
                        //this.modal_data.title += ' > ' + component_data.title;
                        console.log("CASE 3: ", this.index_data.index_market_object);
                        //this.index_data.index_market_object.market = component_data;
                        this.selected_step_component = 'Structure';
                        break;
                    case 4:
                        this.index_data.number_of_dates = 1;
                        if (this.index_data.index_market_object.trade_structure == 'Calendar') {
                            this.index_data.number_of_dates = 2;
                        }
                        //this.modal_data.title += ' > ' + component_data;
                        //this.index_data.index_market_object.trade_structure = component_data;
                        console.log("CASE 4: ", this.index_data.index_market_object);
                        this.selected_step_component = 'Expiry';                   
                        break;
                    case 5:
                        //this.index_data.index_market_object.expiry_dates = component_data;
                        console.log("CASE 5: ", this.index_data.index_market_object);
                        this.selected_step_component = 'Details';
                        console.log("CASE 5 COMPONENT: ", this.selected_step_component);
                        break;
                    case 6:
                        this.modal_data.title = 'Confirm Market Request'
                        //this.index_data.index_market_object.details = component_data;
                        console.log("CASE 6: ", this.index_data.index_market_object);
                        this.selected_step_component = 'Confirm';
                        break;
                    case 7:
                        this.saveMarketRequest();
                    default:
                }
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
             * Saves Market Request
             */
            saveMarketRequest() {
                let new_data = this.formatRequestData();
                axios.post(axios.defaults.baseUrl + '/trade/market/'+ this.index_data.index_market_object.market.id +'/market-request', new_data)
                .then(newMarketRequestResponse => {
                    if(newMarketRequestResponse.status == 200) {
                        //console.log("Saving: ",newMarketRequestResponse);
                        this.close_modal();
                        //this.$root.reloadMarketRequests(); //we using pusher now
                    } else {
                        console.error(err);    
                    }
                }).catch( (err) => {
                    this.previousStep();
                    if (err.response) {
                        // The request was made and the server responded with a status code
                        // that falls out of the range of 2xx
                        this.errors.message = err.response.data.message;
                        this.loadErrorStep(err.response.data.errors);
                    } else if (err.request) {
                      // The request was made but no response was received
                      // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                      // http.ClientRequest in node.js
                      console.log(err.request);
                    } else {
                      // Something happened in setting up the request that triggered an Error
                      console.log('Error', err.message);
                    }
                    console.log(err.config);
                });
            },
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
                            this.setLowestStep(2);
                        }
                    }
                }
                this.loadStepComponent();
            },
            setLowestStep(new_step) {
                if(new_step < this.modal_data.step) {    
                    this.modal_data.step = new_step;
                }
            },
            formatRequestData() {
                let formatted_data = {
                    trade_structure: this.index_data.index_market_object.trade_structure,
                    trade_structure_groups:[]
                }
                this.index_data.index_market_object.details.fields.forEach( (element,key) => {
                    formatted_data.trade_structure_groups.push({
                        is_selected: element.is_selected,
                        market_id: this.index_data.index_market_object.market.id,
                        fields: {
                            "Expiration Date": this.castToMoment( (formatted_data.trade_structure == 'Calendar') ? this.index_data.index_market_object.expiry_dates[key] : this.index_data.index_market_object.expiry_dates[0] ),
                            Strike: element.strike,
                            Quantity: element.quantity
                        }
                    });
                });
                return formatted_data;
            },
            castToMoment(date_string) {
                return moment(date_string, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
            },
        },
        mounted() {
            this.modal_data.title = "Index";
            this.loadIndexMarkets();
            console.log("WHAT STEP IS THIS?==================",this.modal_data.step);
            this.selected_step_component = 'Market';
            this.$on('modal_step', this.loadStepComponent);
        }
    }
</script>