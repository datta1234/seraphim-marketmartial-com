<template>
    <div dusk="step-selections" class="step-selections">
        <b-container fluid>
            <b-row class="mt-2">
                <b-col>
                    <b-button class="mm-modal-market-button w-100" @click="nextStep('Index')">Index Options</b-button>
                </b-col>
                <b-col>
                    <b-button class="mm-modal-market-button w-100">EFP</b-button>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col>
                    <b-button class="mm-modal-market-button w-100">Single Stock Options</b-button>
                </b-col>
                <b-col>
                    <b-button class="mm-modal-market-button w-100">Rolls</b-button>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col>
                    <b-button class="mm-modal-market-button w-100">Options Switch</b-button>
                </b-col>
                <b-col>
                    <b-button class="mm-modal-market-button w-100">EFP Switch</b-button>
                </b-col>
            </b-row>  
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'StepSelection',
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
            };
        },
        methods: {
            nextStep(step) {
                this.modal_data.step++;
                console.log("Current Step===================: ", this.modal_data.step);
                this.callback(step)
            },
        },
        mounted() {
        }
    }
</script>