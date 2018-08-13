<template>
    <div dusk="important-markets-menu" class="important-markets-menu">
        <button id="action-important-button" type="button" class="btn mm-important-button mr-2 p-1" >Important <strong>{{ notifications.length }}</strong></button>
        <div id="important-popover"></div>
        <!-- Important market popover -->
        <b-popover container="important-popover" triggers="click blur" placement="bottom" :ref="popover_ref" target="action-important-button">
            <div class="row text-center">
                <div v-for="(market_request,key) in notifications" class="col-12">
                        <div class="row mt-2">
                          
                          <div class="col-6 text-center  pt-2 pb-2">
                              <h6 class="w-100 m-0 popover-over-text"> {{ market_request.getMarket().title }} {{ market_request.trade_items.default ? market_request.trade_items.default["Strike"] : '' }} {{ market_request.trade_items.default ? market_request.trade_items.default["Expiration Date"] : '' }}</h6>
                          </div>
                          <div class="col-6">
                              <button
                                  :id="'important-nocare-' + market_request.id"
                                  type="button" class="btn mm-generic-trade-button w-100"
                                  @click="addToNoCares(key,market_request.id)"
                                  v-bind:class="{ selected: status }">No Cares
                              </button>
                          </div>
                 
                        </div>
                    </div>
                </div>
                <div class="row text-center">

                <div class="col-12 mt-2">
                    <b-form-group class="bulk-no-cares">
                        <b-form-checkbox 
                            id="select-bulk-nocares"
                            v-model="status" 
                            value="true">
                            Apply No Cares to All
                        </b-form-checkbox>
                    </b-form-group>
                </div>
                     <div class="col-6 mt-1">
                      <button id="apply-bulk-nocares-button" type="button" class="btn mm-generic-trade-button w-100" @click="applyBulkNoCares">OK</button>
                  </div>
                  <div class="col-6 mt-1">
                      <button id="dismiss-important-popover" type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss()">Cancel</button>
                  </div>
                </div>
               

        </b-popover>
        <!-- END Important market popover -->
    </div>
</template>

<script>
    export default {
      name: 'ImportantMenu',
    	props:{
          'notifications': {
            type: Array
          },
          'no_cares': {
            type: Array
          },
        },
        data() {
            return {
                status: false,
                popover_ref: 'important-market-ref',
            };
        },
        methods: {
            /**
             * Closes popover
             */
            onDismiss() {
                this.status = false;
                this.$refs[this.popover_ref].$emit('close');
            },
            /**
             * Adds a single Important UserMarketRequest to no cares list and removes it from Markets array
             *
             * @param {string} $id a string id detailing the UserMarketRequests to be removed
             *
             * @todo Change $market to be the Market.id not Market.title
             */
            addToNoCares(key,id) {

                if(!this.no_cares.includes(id)) {
                    this.no_cares.push(id);
                    this.notifications.splice(key,1);
                    this.saveNoCares();
                }
            },
            /**
             * Adds all Important UserMarketRequest to no cares list and removes them from Markets array and
             *      closes the popover
             *
             * @todo Change market to be the Market.id not Market.title
             */
            applyBulkNoCares() {
                if(this.status) {

                  for (let i=0 ; i < this.notifications.length; i++) {
                    this.no_cares.push(this.notifications[i].id);
                  };
                  this.notifications.splice(0,this.notifications.length);
                  this.saveNoCares();

                }
                // this.onDismiss();
            },
            saveNoCares(){
                const parsed = JSON.stringify(this.no_cares);
                localStorage.setItem('no_cares_market_request', parsed);
            }
            
        },
        mounted() {
        }
    }
</script>