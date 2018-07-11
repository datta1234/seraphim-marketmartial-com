<template>
    <div dusk="structure-selection" class="step-selections">
        <b-container fluid>
            Structure Selection
            <b-row v-if="trade_structures" class="text-center">
                <b-col v-for="trade_structure in trade_structures" v-if="trade_structure.is_selectable" cols="12" class="mt-2">
                    <b-button :id="trade_structure.title+'-structure-choice'" class="mm-modal-market-button-alt w-50" @click="selectStructure(trade_structure.title)">
                        {{ trade_structure.title }}
                    </b-button>
                </b-col>
            </b-row>
            <b-row v-if="errors.messages.length > 0" class="text-center mt-4">
                <b-col v-for="error in errors.messages" cols="12">
                    <p class="text-danger mb-0">{{ error }}</p>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'StructureSelection',
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
                trade_structures: null,
            };
        },
        methods: {
            selectStructure(trade_structure) {
                this.data.index_market_object.trade_structure = trade_structure;
                this.callback(trade_structure);
            },
            /**
             * Loads Market Structure
             */
            loadStructures() {
                axios.get(axios.defaults.baseUrl + '/trade/market-type/'+this.data.market_type.id+'/trade-structure')
                .then(tradeStructureResponse => {
                    if(tradeStructureResponse.status == 200) {
                        this.trade_structures = tradeStructureResponse.data;
                    } else {
                        console.error(err);    
                    }
                }, err => {
                    console.error(err);
                });
            },
        },
        mounted() {
            this.loadStructures();
        }
    }
</script>