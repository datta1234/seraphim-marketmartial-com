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

    import Market from '../../../../../lib/Market';
    export default {
        name: 'IndexController',
        props:{
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
                    market_type_title:"Index Option",
                    market_type: null,
                    index_market_object: {
                        trade_structure: "",
                        trade_structure_groups: [],
                    },
                    number_of_dates: 1,
                },
                selected_step_component: null,
                components: {
                    Selections: StepSelection,
                    Market: MarketSelection,
                    Structure: StructureSelection,
                    Expiry: ExpirySelection,
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
                        this.modal_data.title += ' > ' + component_data;
                        console.log("CASE 3: ", this.index_data.index_market_object);
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
                        console.log("CASE 5: ", component_data);
                        this.selected_step_component = '';
                        break;
                    case 6:
                        this.selected_step_component = '';
                        break;
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
        },
        mounted() {
            this.modal_data.title = "Index";
            this.loadIndexMarkets();
            console.log("WHAT STEP IS THIS?==================",this.modal_data.step);
            this.selected_step_component = 'Market';
        }
    }
</script>