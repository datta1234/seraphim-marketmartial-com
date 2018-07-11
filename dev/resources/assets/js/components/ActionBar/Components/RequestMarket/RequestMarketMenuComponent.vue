<template>
    <div dusk="request-market-menu" class="request-market-menu">
        <b-button :disabled="!$root.market_types.length > 0" class="btn mm-request-button mr-2 p-1" @click="showModal()">Request a Market</b-button>
        
        <!-- Confirmations Modal -->
        <b-modal class="mm-modal mx-auto" size="lg" v-model="modal_data.show_modal" :ref="modal_data.modal_ref">
            <!-- Modal title content --> 
            <div class="mm-modal-title" slot="modal-title">
                {{ modalTitle }}
            </div>

            <component ref="currentController" v-bind:is="controllers[modal_data.selected_controller]" :close_modal="hideModal" :callback="loadController" :modal_data="modal_data"></component>

           <!-- Modal footer content -->
           <div slot="modal-footer" class="w-100">
                <b-row align-v="center">
                    <b-col cols="12">
                        <b-button class="mm-modal-button mr-2 w-25" v-if="modal_data.step > 0" @click="previousStep()">Back</b-button>
                        <b-button class="mm-modal-button ml-2 w-25" @click="hideModal()">Cancel</b-button>
                    </b-col>
                </b-row>
           </div>
        </b-modal>
        <!-- END Confirmations Modal -->
    </div>
</template>

<script>
    import IndexController from './Controllers/IndexControllerComponent.vue';
    import StepSelection from './Components/StepSelectionComponent.vue';
    import MarketSelection from './Components/MarketSelectionComponent.vue';
    import StructureSelection from './Components/StructureSelectionComponent.vue';
    import ExpirySelection from './Components/ExpirySelectionComponent.vue';
    import Details from './Components/DetailsComponent.vue';
    import ConfirmMarketRequest from './Components/ConfirmMarketRequestComponent.vue';

    export default {
        name: 'RequestMarketMenu',
        components: {
            StepSelection,
            IndexController,
            MarketSelection,
            StructureSelection,
            ExpirySelection,
            Details,
            ConfirmMarketRequest,
        },
        props:{
          
        },
        data() {
            return {
                modal_data: {
                    title:'Select A Market',
                    step: 0,
                    show_modal: false,
                    modal_ref: 'request-market-ref',
                    selected_controller: null,
                },
                controllers: {
                    Selections: StepSelection,
                    Index: IndexController,
                },
            };
        },
        computed: {
            modalTitle: function() {
                return this.modal_data.title;
            }
        },
        methods: {
            /**
             * Loads the Reqeust a Market Modal 
             */
            showModal() {
                this.modal_data.selected_controller = 'Selections';
                this.modal_data.step = 0;
                this.modal_data.show_modal = true;
                console.log("RUNNING: ", this.modal_data.selected_controller, this.modal_data.show_modal, this.modal_data.step);
                this.$refs[this.modal_data.modal_ref].$on('hidden', this.hideModal);
            },
            /**
             * Closes the Reqeust a Market Modal 
             */
            hideModal() {
                this.modal_data.step = 0;
                this.modal_data.show_modal = false;
                this.modal_data.title = 'Select A Market';
                this.modal_data.selected_controller = null;
                this.$refs[this.modal_data.modal_ref].$off('hidden', this.hideModal);
            },
            /**
             * Loads component contoller 
             */
            loadController(name) {
                this.nextStep();
                this.modal_data.selected_controller = name;
            },
            /**
             * Returns to previous modal step 
             */
            previousStep() {
                this.modal_data.step--;
                if(this.modal_data.step == 1) {
                    this.modal_data.selected_controller = 'Selections';
                    this.modal_data.step = 0;
                } else {
                    this.$refs['currentController'].$emit('modal_step', 'back');
                }
            },
            nextStep() {
                //receives name of next componenet to load
                this.modal_data.step++;
            },
        },
        mounted() {
        }
    }
</script>