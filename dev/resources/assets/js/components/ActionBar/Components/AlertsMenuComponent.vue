<template>
    <div dusk="alerts-markets-menu" class="alerts-markets-menu">
        <button id="action-alert-button" type="button" class="btn mm-alert-button mr-2 p-1">Alerts <strong>{{ count }}</strong></button>
        <div id="alerts-popover"></div>
        <!-- Alerts market popover -->
        <b-popover container="alerts-popover" triggers="focus" placement="bottom" :ref="popover_ref" target="action-alert-button">
            <div class="row text-center">
                <div v-for="(market_request,key) in notifications" class="col-12">
                    <div class="row mt-1">
                        <div class="col-6 text-center">
                            <h6 class="w-100 m-0">  {{ market_request.getMarket().title }} {{ market_request.trade_items.default ? market_request.trade_items.default["Strike"] : '' }} {{ market_request.trade_items.default ? market_request.trade_items.default["Expiration Date"] : '' }}
                          </h6>
                        </div>
                        <div class="col-6">
                            <button
                                :id="'alert-view-' + market_request.id"
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="loadInteractionBar(market_request)">View
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-6 offset-6 mt-1">
                    <button id="dismiss-alert-popover" type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss">OK</button>
                </div>
            </div>
        </b-popover>
        <!-- END Alerts market popover -->
    </div>
</template>

<script>
    import { EventBus } from '../../../lib/EventBus.js';
    export default {
      name: 'AlertsMenu',
      props:{
          'notifications': {
            type: Array
          }
        },
        data() {
            return {
               popover_ref: 'alert-market-ref',
            };
        },
        methods: {
            /**
             * Closes popover
             */
            onDismiss() {
                this.$refs[this.popover_ref].$emit('close');
            },
             /**
             * Loads the Interaction Sidebar with the related UserMarketRequest
             *
             * @param {/lib/UserMarketRequest} $market_request the UserMarketRequest that need to be passed
             *      to the Interaction Sidebar.
             *
             * @fires /lib/EventBus#toggleSidebar
             */
            loadInteractionBar(market_request) {
                EventBus.$emit('toggleSidebar', 'interaction', true, market_request);
            },
        },
        mounted() {
            
        }
    }
</script>