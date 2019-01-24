<template>
       <b-popover ref="popover" :target="target" placement="bottom" :show.sync="open" triggers="" :container="parent">
            <template slot="title">
               <b-row no-gutters>
                  <b-col class="text-center">
                    {{ title }}
                  </b-col>
                  <b-col cols="auto">
                    <a @click="cancel" class="close" aria-label="Close">
                      <span class="d-inline-block" aria-hidden="true">&times;</span>
                    </a>
                  </b-col>
               </b-row>
            </template>
            <b-row> 
                <b-col cols="12" v-html="subtitle" class="text-center"></b-col>
            </b-row>
            <template v-if="is_offer == null">
              <b-row>
                 <b-col cols="12">
                      <b-btn v-active-request block variant="primary" size="sm" @click="is_offer = true">Buy</b-btn>
                 </b-col>
                 <b-col cols="12">
                       <b-btn class="mt-2" block v-active-request variant="primary" size="sm" @click="is_offer = false">Sell</b-btn>
                 </b-col>
              </b-row>
            </template>
            <template v-if="is_offer != null && !confirmed">
               <b-row>
                 <b-col cols="12">
                       <b-btn v-active-request variant="primary" block @click="confirmed = true">{{ btnText }}</b-btn>
                 </b-col>
                  <b-col class="mt-2"  cols="12">
                      <b-btn v-active-request variant="danger" block @click="cancel()">Cancel</b-btn>
                 </b-col>
              </b-row>
            </template>
            <template v-if="confirmed">
            <div class="form-row align-items-center">
                 <label class="col-sm-4">
                  Desired Quantity
                </label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="inlineFormInputName" v-if="tradeNegotiation" v-model="tradeNegotiation.quantity">
                </div>
                <label class="col-sm-4">
                    {{ quantityType }}
                </label>
            </div>
                <b-row>
                    <b-col cols="12" :key="key" v-for="(error,key) in errors" class="text-danger">
                        {{ error[0] }}
                    </b-col>
                    <b-col cols="12">       
                        <b-btn v-active-request variant="primary" class="btn-block mt-3" :disabled="server_loading" @click="storeTradeNegotiation()"> Send Desired Size</b-btn>
                    </b-col>

                </b-row>
            </template>
      </b-popover>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import TradeNegotiation from '~/lib/TradeNegotiation';

    export default {
        name: 'ibar-trade-desired-quantity',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null,
            },
            target: {
                type: String,
                default: null,
            },
            open: {
                type: Boolean,
                default: false,
            },
            isOffer: {
                type: Boolean,
                default: null,
            },
            parent: {
                type: String,
                default: "",
            }
        },
        data() {
             return {
                is_offer: null,
                 confirmed:false,
                 tradeNegotiation: new TradeNegotiation(),
                 server_loading: false,
                 errors: []
            };
        },
        watch: {
            marketNegotiation: function () {
                if(this.is_offer)
                {
                  this.tradeNegotiation.quantity = this.marketNegotiation.offer_qty;
                }else
                {
                  this.tradeNegotiation.quantity = this.marketNegotiation.bid_qty;
                }

                this.reset();
            }
        },
        computed: {
           title() {
                let marketRequest = this.marketNegotiation.getUserMarket().getMarketRequest();
                let title = marketRequest.marketTradeTitle();
                
                if(this.is_offer == null) {
                  return "Buy Or Sell?"
                }
                if(this.is_offer)
                {
                    return this.confirmed ?  title : "Lift the offer?";   
                }
                else
                {
                    return this.confirmed ?  title : "Hit the bid?";   
                }
           },
           subtitle(){

            if(this.is_offer){
               return this.confirmed ? "You Are Buying @ <span class='text-success'>" + this.marketNegotiation.offer +'</span>': "";
            }else
            {
              return this.confirmed ? "You Are Selling @ <span class='text-success'>" + this.marketNegotiation.bid +'</span>': "";

            }

           },
          quantityType: function(){
              return this.marketNegotiation.getQuantityType();
          },
           btnText(){
                return this.is_offer ? "Buy" : "Sell";
           }            
        },
        methods: {
          reset() {
                 this.is_offer = this.isOffer;
                 this.confirmed =false;
                 this.tradeNegotiation = new TradeNegotiation();
                 this.server_loading = false;
                 this.errors = [];
          },
            cancel(){
                this.$emit('close');
                this.is_offer = this.isOffer;
                this.confirmed = false;
            },
           storeTradeNegotiation(){
                this.server_loading = true;
                this.tradeNegotiation.is_offer = this.is_offer;

                this.tradeNegotiation.store(this.marketNegotiation)
                .then(response => {
                    this.errors = [];
                    this.$emit('close');
                    this.is_offer = this.isOffer;
                    this.confirmed = false;
                    this.server_loading = false;
                })
                .catch(err => {

                    this.history_message = err.errors.message;
                    this.errors = err.errors;
                    this.server_loading = false;

                });
           }
        },
        mounted() {
            this.is_offer = this.isOffer;

            if(this.is_offer)
            {
              this.tradeNegotiation.quantity = this.marketNegotiation.offer_qty;
            }else
            {
              this.tradeNegotiation.quantity = this.marketNegotiation.bid_qty;
            }

        }
    }
</script>
