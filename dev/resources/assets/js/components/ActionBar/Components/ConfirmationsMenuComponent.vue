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
                            <h6 class="w-100 m-0 popover-over-text">  {{ trade_confirmation.title }}
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
        <!-- END Confirmations market popover -->

        <!-- Confirmations Modal -->
        <b-modal class="mm-modal mx-auto" size="lg" v-model="modals.show_modal" v-if="modals.show_modal">
            <!-- Modal title content --> 
            <div slot="modal-title">
                {{ modals.trade_confirmation.title }}
            </div>
           
            <!-- Modal body content -->
            <b-container fluid>
                <p>Thank you for your trade! Please check before accepting.</p>
                <p>Date: {{ modals.trade_confirmation.readableExpiration() }} </p>
                <p>Structure: {{ modals.trade_confirmation.trade_structure_title }}</p>
                <div style="Display:inline;">
                    <h3>Option</h3>
                </div>

                <table class="table table-sm">
                  <thead>
                    <tr>
                        <th scope="col">Bank</th>
                        <th scope="col">Underlying</th>
                        <th scope="col">Strike</th>
                        <th scope="col">Put/Call</th>
                        <th scope="col">Nominal</th>
                        <th scope="col">Contracts</th>
                        <th scope="col">Expiry</th>
                        <th scope="col">Volatility</th>
                        <th scope="col">Gross Prem</th>
                        <th scope="col">Net Prem</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <td>
                            
                        </td>
                        <td>
                            {{ modals.trade_confirmation.underlying_title }}
                        </td>
                        <td>
                            {{ modals.trade_confirmation.strike }}
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            {{ modals.trade_confirmation.contracts }}
                        </td>
                        <td>
                            {{ modals.trade_confirmation.readableExpiration() }}
                        <td>
                            {{ modals.trade_confirmation.volatility }}                            
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            
                        </td>
                    </tr>
                  </tbody>
                </table>

                <div>
                    <h3>Futures</h3>
                </div>

                  <table class="table table-sm">
                  <thead>
                    <tr>
                        <th scope="col">Bank</th>
                        <th scope="col">Underlying</th>
                        <th scope="col">Spot</th>
                        <th scope="col">Future</th>
                        <th scope="col">Contracts</th>
                        <th scope="col">Expiry</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <td>
                            <!-- blank -->
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            <b-form-input v-model="proposed_trade_confirmation.spot_price" type="text"></b-form-input>
                        </td>
                        <td>
                            <b-form-input v-model="proposed_trade_confirmation.future_reference" type="text"></b-form-input>
                        </td>
                        <td>
                          <b-form-input :disabled="!calculated" v-model="proposed_trade_confirmation.contracts" type="text"></b-form-input>  
                        </td>
                        <td>
                          <b-form-input :disabled="!calculated" v-model="proposed_trade_confirmation.near_expiery_reference" type="text"></b-form-input>      
                        </td>
                    </tr>
                  </tbody>
                </table>

                <b-row>
                    <b-col md="5" offset-md="7">
                        <button type="button" class="btn mm-generic-trade-button w-100 mb-1">Update and calculate</button>
                        <button type="button" class="btn mm-generic-trade-button w-100 mb-1">Send to counterparty</button>
                         <div class="form-group">
                            <label for="exampleFormControlSelect1">Account Booking</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                              <option>1</option>
                              <option>2</option>
                              <option>3</option>
                              <option>4</option>
                              <option>5</option>
                            </select>
                          </div>
                    </b-col>
                </b-row>
            </b-container>
           <!-- Modal footer content -->
           <div slot="modal-footer" class="w-100">
                <!-- <b-button @click="show_modal=false">Close</b-button> -->
           </div>
        </b-modal>
        <!-- END Confirmations Modal -->
        </div>
</template>

<script>
    import UserMarketRequest from '../../../lib/UserMarketRequest';
    import TradeConfirmation from '../../../lib/TradeConfirmation';

    export default {
        name: 'ConfirmationsMenu',
        props:{
            'trade_confirmations': {
                type: Array
            }
        },
        data() {
            return {
                modals: {
                    show_modal: false,
                    trade_confirmation: {
                        type: UserMarketRequest
                    }
                },
                popover_ref: 'confirmation-market-ref', 
                proposed_trade_confirmation: new TradeConfirmation()
            };
        },
        computed: {
            'calculated': function(){
                return false;
            }
        },
        methods: {
            /**
             * Closes popover
             */
            onDismiss() 
            {
                this.$refs[this.popover_ref].$emit('close');
            },
            setUp(trade_confirmation)
            {
                
            },
            /**
             * Loads the Confirmation Modal with the related UserMarketRequest 
             *
             * @param {/lib/UserMarketRequest} $trade_confirmation the UserMarketRequest that need to be passed
             *      to the Interaction Sidebar.
             */
            loadModal(trade_confirmation) 
            {
                console.log("printing data");
                this.modals.trade_confirmation = trade_confirmation;
                this.modals.show_modal = true;
                this.proposed_trade_confirmation = new TradeConfirmation();
                this.proposed_trade_confirmation.phaseOneRun(this.modals.trade_confirmation);

            },
        },
        mounted() {
        }
    }
</script>