<template>
    <div dusk="step-selection" class="step-selections">
        <b-container fluid>
            <b-row class="mt-2">
                <b-col>
                    <b-button id="index-step-choice" class="mm-modal-market-button-alt w-100" @click="nextStep('Index')">Index Options</b-button>
                </b-col>
                <b-col>
                    <b-button id="efp-step-choice" class="mm-modal-market-button-alt w-100" @click="nextStep('EFP')">EFP</b-button>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col>
                    <b-button id="single-stock-step-choice" class="mm-modal-market-button-alt w-100" @click="nextStep('Single')">Single Stock Options</b-button>
                </b-col>
                <b-col>
                    <b-button id="rolls-step-choice" class="mm-modal-market-button-alt w-100" @click="nextStep('Rolls')">Rolls</b-button>
                </b-col>
            </b-row>
            <b-row class="mt-2">
                <b-col>
                    <b-button id="options-switch-step-choice" class="mm-modal-market-button-alt w-100" @click="nextStep('OptionSwitch')">Options Switch</b-button>
                </b-col>
                <b-col>
                    <b-button id="efp-switch-step-choice" class="mm-modal-market-button-alt w-100">EFP Switch</b-button>
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
            /**
             * Sets the inital market step and calls the component call back method
             */
            nextStep(step) {
                this.modal_data.step++;
                this.callback(step)
            },
        },
        mounted() {
        }
    }
</script>