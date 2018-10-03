<template>
       <div>
        {{ lastTradeNegotiation.getTextOver() }} would you like to work the balance?

          <b-row class="justify-content-md-center">
              <div cols="4">
                  <b-form-input class="mb-5" type="text" v-model="quantity" placeholder="Contracts"></b-form-input>
              </div>
              <label class="col-4 col-form-label">Contracts</label>
          </b-row>
          <b-row class="justify-content-md-center">
              <b-col cols="6">
                    <b-button  class="w-100 mt-1" 
                                   :disabled="server_loading || quantity <= 0" 
                                   size="sm" 
                                   dusk="ibar-work-balance" 
                                   variant="success" 
                                   @click="storeWorkBalance()">
                                      Work the balance
                      </b-button>
                       <b-button  class="w-100 mt-1" 
                                   :disabled="server_loading || quantity > 0" 
                                   size="sm" 
                                   dusk="ibar-no-further-balance" 
                                   variant="danger" 
                                   @click="noFutherCares()">
                                      No Further cares
                      </b-button>
              </b-col>
          </b-row>
      </div>
</template>
<script>
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';

    export default {
        name: 'ibar-trade-work-balance',
        props: {
            marketRequest: {
                type: UserMarketRequest,
                default: null,
            }
        },
        data() {
             return {
                 marketNegotiation: new UserMarketNegotiation(),
                 server_loading: false,
                 errors: [],
                 quantity:null

            };
        },
        watch: {
            marketRequest: function () {
              this.calcQuantity();
            }
        },
        computed: {
           lastTradeNegotiation(){
            
             return this.marketRequest.chosen_user_market.getLastNegotiation().getLastTradeNegotiation();
          }        
        },
        methods: {
          calcQuantity(){
              this.quantity = this.lastTradeNegotiation.getQuantityOver(); 
          },
          storeWorkBalance(){
                this.server_loading = true;
                let userMarket = this.marketRequest.chosen_user_market;
                this.marketNegotiation.storeWorkBalance(userMarket,this.quantity)
                .then(response => {
                    this.server_loading = false;
                    this.errors = [];
                })
                .catch(err => {
                    this.server_loading = false;

                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });
           },
          noFutherCares(){
                this.server_loading = true;
                this.marketRequest.chosen_user_market.noFutherCares()
                .then(response => {
                    this.server_loading = false;
                    this.errors = [];
                })
                .catch(err => {
                    this.server_loading = false;

                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });
          }
        },
        created() {
          this.calcQuantity();
        }
    }
</script>
