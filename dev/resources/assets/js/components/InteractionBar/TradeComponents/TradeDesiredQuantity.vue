<template>
       <b-popover ref="popover" :target="target" placement="bottom" :show.sync="open" triggers="" :container="parent">
            <template slot="title">
               <div class="text-center">{{ title }}
                   <a @click="cancel" class="close" aria-label="Close">
                    <span class="d-inline-block" aria-hidden="true">&times;</span>
                    </a>
               </div>
            
            </template>
            <b-row> 
                <b-col cols="12" v-html="subtitle" class="text-center"></b-col>
            </b-row>
            <template v-if="is_offer == null">
              <b-row>
                 <b-col cols="6">
                      <b-btn v-active-request variant="primary" size="sm" @click="is_offer = true">Buy</b-btn>
                 </b-col>
                 <b-col cols="6">
                       <b-btn v-active-request variant="primary" size="sm" @click="is_offer = false">Sell</b-btn>
                 </b-col>
              </b-row>
            </template>
            <template v-if="is_offer != null && !confirmed">
              <b-row>
                 <b-col cols="12" class="text-center">
                      Confirm {{ btnText }}
                 </b-col>
               </b-row>
               <b-row>
                 <b-col cols="6">
                      <b-btn v-active-request variant="danger"  size="sm" @click="cancel()">Cancel</b-btn>
                 </b-col>
                 <b-col cols="6">
                       <b-btn v-active-request variant="primary" size="sm" @click="confirmed = true">{{ btnText }}</b-btn>
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
                  Contracts
                </label>

            </div>
                <b-row>
                    <b-col cols="12" :key="key" v-for="(error,key) in errors" class="text-danger">
                        {{ error[0] }}
                    </b-col>
                    <b-col cols="12">       
                        <b-btn v-active-request variant="primary" class="btn-block mt-3" :disabled="server_loading" @click="storeTradeNegotiation()"> Send Desired SIze</b-btn>
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
            }
        },
        computed: {
           title(){

                let marketRequest = this.marketNegotiation.getUserMarket().getMarketRequest();
                let marketTitle = marketRequest.getMarket().title
                let title = marketTitle+" "+marketRequest.trade_items.default[this.$root.config("trade_structure.outright.expiration_date")]+
                " "+marketRequest.trade_items.default[this.$root.config("trade_structure.outright.strike")];
                
                if(this.is_offer == null) {
                  return "Buy Or Sell?"
                }
                if(this.is_offer)
                {
                    return this.confirmed ?  title : "Lift the offer?";   
                }else
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
           btnText(){
                return this.is_offer ? "Buy" : "Sell";
           }            
        },
        methods: {
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
                    this.server_loading = false;
                    this.errors = [];
                    this.$emit('close');
                    this.is_offer = this.isOffer;
                    this.confirmed = false;
                })
                .catch(err => {
                    this.server_loading = false;

                    this.history_message = err.errors.message;
                    this.errors = err.errors.errors;
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
