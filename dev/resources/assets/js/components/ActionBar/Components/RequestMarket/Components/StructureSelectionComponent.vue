<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row v-if="trade_structures" class="justify-content-md-center">
                <b-col v-for="trade_structure in trade_structures" v-if="trade_structure.is_selectable" cols="6" class="mt-2">
                    <b-button class="mm-modal-market-button w-100" @click="selectStructure(trade_structure.title)">
                        {{ trade_structure.title }}
                    </b-button>
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
            }
        },
        data() {
            return {
                trade_structures: null,
            };
        },
        methods: {
            selectStructure(trade_structure) {
                this.callback(trade_structure);
            },
            /**
             * Loads Market Structure
             *
             * @todo move to component, logic does not need to be here
             */
            loadStructures() {
                axios.get('trade/market-type/'+this.data.market_type.id+'/trade-structure')
                .then(tradeStructureResponse => {
                    if(tradeStructureResponse.status == 200) {
                        this.trade_structures = tradeStructureResponse.data;
                        console.log("WHAT COMES FROM SERVER STRUCTURE? ",this.trade_structures);
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