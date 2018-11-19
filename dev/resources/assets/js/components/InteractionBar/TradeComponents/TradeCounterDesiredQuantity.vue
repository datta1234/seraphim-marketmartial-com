<template>
    <div v-if="canCounterTrade">
        <b-row class="justify-content-md-center">
            <div cols="4">
                <b-form-input v-active-request v-model="tradeNegotiation.quantity" class="mb-5" type="text" :placeholder="quantityType"></b-form-input>
            </div>
            <label class="col-4 col-form-label">{{ quantityType }}</label>
        </b-row>
        <b-row class="justify-content-md-center">
            <b-col cols="6">

                  <b-button v-active-request  class="w-100 mt-1" 
                                 :disabled="server_loading || amendInput()" 
                                 size="sm" 
                                 dusk="ibar-send-counter" 
                                 variant="success" 
                                 @click="storeTradeNegotiation()">
                                    Trade requested size
                    </b-button>
                     <b-button v-active-request  class="w-100 mt-1" 
                                 :disabled="server_loading || !amendInput()" 
                                 size="sm" 
                                 dusk="ibar-send-amend" 
                                 variant="primary" 
                                 @click="storeTradeNegotiation()">
                                    Send amended size
                    </b-button>
            </b-col>
        </b-row>
    </div>
</template>
<script>
    import UsermarketRequest from '~/lib/UserMarketRequest';
    import TradeNegotiation from '~/lib/TradeNegotiation';

    export default {
        name: 'ibar-trade-desired-quantity',
        props: {
            marketRequest: {
                type: UsermarketRequest,
                default: null,
            }
        },
        watch:{
            marketRequest:{
                handler: function (val, oldVal) { 
                    this.tradeNegotiation.quantity = this.lastTradeNegotiation.quantity;
                    this.tradeNegotiation.setUserMarket(this.lastTradeNegotiation.user_market_negotiation);
                },
                deep: true
            }
        },
        data() {
             return {
                 confirmed:false,
                 tradeNegotiation: new TradeNegotiation(),
                 server_loading: false,
                 errors: []

            };
        },
        computed: {
            canCounterTrade(){
               return this.marketRequest.canCounterTrade();
            },
           lastTradeNegotiation(){
             return this.marketRequest.chosen_user_market.getLastNegotiation().getLastTradeNegotiation();
           },
           quantityType(){
            return this.marketRequest.getQuantityType();
           }     
        },
        methods: {
            amendInput(){
                return this.tradeNegotiation.quantity > this.lastTradeNegotiation.quantity
            },
           storeTradeNegotiation(){
                this.server_loading = true;
                this.tradeNegotiation.is_offer = this.isOffer;


                /*
                ========sent=========
                Opting to amend the quantity HIGHER results in the initiating party receiving an alert, where the market tab turns red. This party can opt to “Trade input size”, or can amend the quantity to the HIGHER amount. When the quantity is amended to a HIGHER amount,

                “Send amended size” button is activated, and the “Trade input size” is deactivated.
                */

                /*


                */


                this.lastTradeNegotiation.counter(this.tradeNegotiation)
                .then(response => {
                    this.server_loading = false;
                    this.errors = [];
                    this.$emit('close');
                })
                .catch(err => {
                    this.server_loading = false;

                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
                });
           }
        },
        created() {
            this.tradeNegotiation.quantity = this.lastTradeNegotiation != null ? this.lastTradeNegotiation.quantity : null;

            this.tradeNegotiation.setUserMarket(this.lastTradeNegotiation.user_market_negotiation);
        }
    }
</script>
