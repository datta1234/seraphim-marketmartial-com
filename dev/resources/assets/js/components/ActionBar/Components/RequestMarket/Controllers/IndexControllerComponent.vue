<template>
    <div dusk="index-controller" class="index-controller">
        <component v-bind:is="components[selected_step_component]" :data="index_data" :callback="loadStepComponent"></component>
    </div>
</template>

<script>
    import StepSelection from '../Components/StepSelectionComponent.vue';
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
                selected_step_component: null,
                components: {
                    Selections: StepSelection,
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

                this.nextStep();
                switch (this.modal_data.step) {
                    case 1:
                        this.selected_step_component = 'Selections';    
                        break;
                    case 2:
                        this.selected_step_component = 'Market';
                        break;
                    case 3:
                        this.modal_data.title += ' > ' + component_data.title;
                        console.log("CASE 3: ", this.index_data.index_market_object);
                        this.index_data.index_market_object.market = component_data;
                        this.selected_step_component = 'Structure';
                        break;
                    case 4:
                        if (component_data == 'Calendar') {
                            this.index_data.number_of_dates = 2;
                        }
                        this.modal_data.title += ' > ' + component_data;
                        this.index_data.index_market_object.trade_structure = component_data;
                        console.log("CASE 4: ", this.index_data.index_market_object);
                        this.selected_step_component = 'Expiry';                   
                        break;
                    case 5:
                        this.index_data.index_market_object.expiry_dates = component_data;
                        console.log("CASE 5: ", this.index_data.index_market_object);
                        this.selected_step_component = 'Details';
                        console.log("CASE 5 COMPONENT: ", this.selected_step_component);
                        break;
                    case 6:
                        this.modal_data.title = 'Confirm Market Request'
                        this.index_data.index_market_object.details = component_data;
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
                console.log("FINAL DATA WE SENDIN: ", new_data);
                axios.post('trade/market/'+ this.index_data.index_market_object.market.id +'/market-request', new_data)
                .then(newMarketRequestResponse => {
                    if(newMarketRequestResponse.status == 200) {
                        console.log("YAY IT SAVES YO!: ",newMarketRequestResponse);
                        this.close_modal();
                    } else {
                        console.error("NOOOOOOOOOOOOOOO!!!!!!!!",err);    
                    }
                }, err => {
                    console.error("EVEN MORE NOOOOOOOOOOOOOOO!!!!!!!!",err);
                });
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
        }
    }
</script>