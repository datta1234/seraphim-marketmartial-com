<template>
    <div dusk="confirmations-markets-menu" class="confirmations-markets-menu">
        <button id="action-confirmations-button" type="button" class="btn mm-confirmation-button mr-2 p-1">Confirmations <strong>{{ trade_confirmations.length }}</strong></button>
        <div id="confirmations-popover"></div>
        <!-- Confirmations market popover -->
        <b-popover container="confirmations-popover" triggers="click blur" placement="bottom" :ref="popover_ref" target="action-confirmations-button">
            <div class="row text-center">
                <div v-for="trade_confirmation in trade_confirmations" class="col-12">
                   <div class="row mt-1">
                        <div class="col-6 text-center pt-2 pb-2">
                            <h6 class="w-100 m-0 popover-over-text">  {{ trade_confirmation.underlying_title +' @'+' '+trade_confirmation.volatility }}
                          </h6>
                        </div>
                        <div class="col-6">
                            <button
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="loadModal(trade_confirmation)">View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </b-popover>
        
           <b-modal @hidden="hideModal()" class="mm-modal mx-auto modal-xl" ref="confirmationModelRef"  hide-footer>
                <!-- Modal title content --> 
                <div class="mm-modal-title" slot="modal-title">
                    Confirmation
                </div>
                <trade-confirmation-component @close="hideModal()" v-if="selected_trade_confirmation != null" :trade_confirmation="selected_trade_confirmation"></trade-confirmation-component>
            </b-modal>

        </div>
</template>

<script>
    import TradeConfirmationComponent from './TradeConfirmation/TradeConfirmationComponent.vue';

    import UserMarketRequest from '../../../lib/UserMarketRequest';
    import TradeConfirmation from '../../../lib/TradeConfirmation';
    // Vue.component('action-bar', require('./components/ActionBarComponent.vue'));

    export default {
        components: {
            'trade-confirmation-component':TradeConfirmationComponent,
        },
        name: 'ConfirmationsMenu',
        props:{
            'trade_confirmations': {
                type: Array
            }
        },
        data() {
            return {
                popover_ref: 'confirmation-market-ref',
                selected_trade_confirmation: null
            };
        },
        methods: {
            loadModal(trade_confirmation)
            {
                this.selected_trade_confirmation = trade_confirmation;
                this.$refs.confirmationModelRef.show();
            },
            hideModal()
            {
                this.$refs.confirmationModelRef.hide()  ;
                this.selected_trade_confirmation = null;
            }
        },
        mounted() {
        }
    }
</script>