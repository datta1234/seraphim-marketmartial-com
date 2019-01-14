<template>
    <b-container fluid class="trade-at-best-deisred-qty p-0">
        <b-row> 
            <b-col cols="12" v-html="subtitle" class="text-center"></b-col>
        </b-row>
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
    </b-container>
</template>
<script type="text/javascript">
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import TradeNegotiation from '~/lib/TradeNegotiation';

    export default {
        name: 'ibar-trade-desired-quantity',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null,
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
           subtitle(){

            if(this.is_offer){
               return "You Are Buying @ <span class='text-success'>" + this.marketNegotiation.offer +'</span>';
            }else
            { 
              return "You Are Selling @ <span class='text-success'>" + this.marketNegotiation.bid +'</span>';

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
                    this.errors = err.errors;

                });
           }
        },
        mounted() {
            this.is_offer = this.marketNegotiation.cond_buy_best;

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