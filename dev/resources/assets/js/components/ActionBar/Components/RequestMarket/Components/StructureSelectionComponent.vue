<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row v-if="data" class="justify-content-md-center">
                <b-col v-for="trade_structure in data.trade_structures" v-if="trade_structure.is_selectable" cols="6" class="mt-2">
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
        components: {
            
        },
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
            };
        },
        methods: {
            selectStructure(trade_structure) {
                this.callback(trade_structure);
            },
        },
        mounted() {
            
        }
    }
</script>