<template>
    <div class="Confirmations-markets-menu">
        <button id="actionConfirmationsButton" type="button" class="btn mm-confirmation-button mr-2 p-1">Confirmations <strong>{{ count }}</strong></button>
        <div id="confirmationsPopover"></div>
        <!-- Confirmations market popover -->
        <b-popover container="confirmationsPopover" triggers="focus" placement="bottom" :ref="popover_ref" target="actionConfirmationsButton">
            <div class="row text-center">
                <div v-for="(maket,key) in notificationList" class="col-12">
                    <div v-if="maket.length > 0" v-for="market_request in maket" class="row mt-1">
                        <div class="col-6 text-center">
                            <h6 class="w-100 m-0"> {{ key }} {{ market_request.attributes.strike }} {{ market_request.attributes.expiration_date.format("MMM DD") }}</h6>
                        </div>
                        <div class="col-6">
                            <button 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="loadModal(market_request)">View
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-6 offset-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss">OK</button>
                </div>
            </div>
        </b-popover>
        <!-- END Confirmations market popover -->

        <!-- Confirmations Modal -->
        <b-modal class="confirmations-modal" size="lg" v-model="modals.show_modal" v-if="modals.show_modal">
            <!-- Modal title content --> 
            <div slot="modal-title">
                {{ modals.market_request.attributes.strike }}
            </div>
           
            <!-- Modal body content -->
            <b-container fluid>
                <p>Thank you for your trade! Please check before accepting.</p>
                <p>Date: {{ modals.market_request.attributes.expiration_date.format('DD-MMM-YYYY') }}</p>
                <p>Structure: </p>
                <div style="Display:inline;">
                    <h3>Option</h3>
                </div>
                <b-table responsive hover :items="modals.table_data.options"></b-table>
                <div style="Display:inline;">
                    <h3>Futures</h3>
                </div>
                <b-table responsive hover :items="modals.table_data.futures"></b-table>
                <b-row>
                    <b-col md="5" offset-md="7">
                        <button type="button" class="btn mm-generic-trade-button w-100 mb-1">I'm Happy, Trade Confirmed</button>
                        <button type="button" class="btn mm-generic-trade-button w-100 mb-1">Delta / Refs - Recalculate & Dispute</button>
                        <button type="button" class="btn mm-generic-trade-button w-100">Send</button>
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
    import { EventBus } from '../../../lib/EventBus.js';
    const UserMarketRequest = require('../../../lib/UserMarketRequest');
    const place_holder_data_options = [
        { 'Bank ABC': 'N/A', 'Underlying': 'N/A', 'Strike': 'N/A', 'Put/Call': 'N/A', 'Nominal': 'N/A', 'Contracts': 'N/A', 'Expiry': 'N/A', 'Volatility': 'N/A', 'Gross Prem': 'N/A', 'Net Prem': 'N/A' },
    ];
    const place_holder_data_futures = [
        { 'Bank ABC': 'N/A', 'Underlying': 'N/A', 'Spot': 'N/A', 'Future': 'N/A', 'Contracts': 'N/A', 'Expriry': 'N/A' },
    ];
    export default {
      props:{
          'markets': {
            type: Array
          },
          'count': {
            type: Number
          },
        },
        data() {
            return {
                modals: {
                    show_modal: false,
                    market_request: {
                        type: UserMarketRequest
                    },
                    table_data: {
                        options:place_holder_data_options,
                        futures:place_holder_data_futures,
                    },
                },
                popover_ref: 'confirmation-market-ref', 
            };
        },
        computed: {
            /**
             * Compiles a notification list for Confirmation market reqeusts with Market as key
             *      and a market requests array as value
             *
             * @return {Object} in format {/lib/Market.title: /lib/UserMarketRequest [] }
             */
            notificationList: function() {
                //Iterates through an array of Markets and compiles an object with Market.title as key
                return this.markets.reduce( function(acc, obj) {
                    //Iterates through an array of UserMarketRequests and compiles a new array of Important UserMarketRequests 
                    acc[obj.title] = obj.market_requests.reduce( function(acc2, obj2) {
                        switch(obj2.attributes.state) {    
                            case "confirm":
                                return acc2.concat(obj2);
                            break;
                            default:
                                return acc2;
                        }
                    }, []);
                    return acc;
                }, {});
            },
        },
        methods: {
            /**
             * Closes popover
             */
            onDismiss() {
                this.$refs[this.popover_ref].$emit('close');
            },
            /**
             * Loads the Confirmation Modal with the related UserMarketRequest 
             *
             * @param {/lib/UserMarketRequest} $market_request the UserMarketRequest that need to be passed
             *      to the Interaction Sidebar.
             */
            loadModal(market_request) {
                console.log("Passed",market_request);
                this.modals.market_request = market_request;
                this.modals.show_modal = true;
            },
        },
        mounted() {
            
        }
    }
</script>