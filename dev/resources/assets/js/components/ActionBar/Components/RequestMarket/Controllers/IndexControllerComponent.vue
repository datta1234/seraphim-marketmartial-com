<template>
    <div dusk="index-controller" class="index-controller">
        <component v-bind:is="components[selected_step_component]" :data="index_data" :callback="loadStepComponent"></component>
    </div>
</template>

<script>
    import StepSelection from '../Components/StepSelectionComponent.vue';
    import MarketSelection from '../Components/MarketSelectionComponent.vue';
    import StructureSelection from '../Components/StructureSelectionComponent.vue';

    import Market from '../../../../../lib/Market';
    /*market/{marketId}/market-request
    'trade_structure':'Outright'
    'trade_structure_groups':
    [
        {
            is_selected: bool,
            fields:{
                'Expiration Date':
                'Strike':
                'Quantity':
            }
        },
    ]
    }*/
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
                    trade_structures: null,
                },
                selected_step_component: null,
                components: {
                    Selections: StepSelection,
                    Market: MarketSelection,
                    Structure :StructureSelection,
                },
                index_market_object: {
                    trade_structure: "Index Option",
                    trade_structure_groups: '',
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
                        console.log("CASE 2: ", this.index_market_object)
                        this.selected_step_component = 'Market';
                        break;
                    case 3:
                        console.log("CASE 3: ", this.index_market_object);
                        this.loadStructures();
                        this.selected_step_component = 'Structure';
                        break;
                    case 4:
                        this.index_market_object.trade_structure = component_data;
                        console.log("CASE 4: ", this.index_market_object);
                        this.selected_step_component = '';                      
                        break;
                    case 5:
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
            /**
             * Loads Market Structure
             */
            loadStructures() {
                axios.get('trade/market-type/'+this.index_data.market_type.id+'/trade-structure')
                .then(tradeStructureResponse => {
                    if(tradeStructureResponse.status == 200) {
                        this.index_data.trade_structures = tradeStructureResponse.data;
                        console.log("WHAT COMES FROM SERVER? ",this.index_data.trade_structures)
                    } else {
                        console.error(err);    
                    }
                }, err => {
                    console.error(err);
                });
            },
        },
        mounted() {
            this.loadIndexMarkets();
            console.log("WHAT STEP IS THIS?==================",this.modal_data.step);
            this.selected_step_component = 'Market';
        }
    }
</script>