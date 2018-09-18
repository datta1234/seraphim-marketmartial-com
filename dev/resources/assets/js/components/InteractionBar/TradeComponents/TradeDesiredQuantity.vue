<template>
       <b-popover ref="popover" :target="target" placement="bottom" :show.sync="open"  triggers="">
            <template slot="title" >
               <strong class="text-center">{{ title }}</strong>
               <b-btn @click="cancel" class="close" aria-label="Close">
                  <span class="d-inline-block" aria-hidden="true">&times;</span>
                </b-btn>
            </template>
            <b-row> 
                <b-col cols="12" v-html="subtitle" class="text-center"></b-col>
            </b-row>   
            <b-row v-if="!confirmed">
               <b-col cols="6">
                    <b-btn variant="danger"  @click="cancel()">Cancel</b-btn>
               </b-col>
               <b-col cols="6">
                     <b-btn variant="success" @click="confirmed = true">{{ btnText }}</b-btn>
               </b-col>
            </b-row>
            <template v-if="confirmed">
                <b-row>
                    <b-col cols="12">
                    <b-form-group label-for="pop1"
                           horizontal
                          description="Contracts"
                          invalid-feedback="This field is required">
                          <template slot="label">Desired Size</template>
                        <b-form-input ref="input1" id="pop1" size="sm" class="mt-2"/>
                    </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12">       
                        <b-btn variant="success" class="btn-block"> Send Contracts</b-btn>
                    </b-col>
                </b-row>
            </template>
      </b-popover>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
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
            isBid: {
                type: Boolean,
                default: false,
            }
        },
        data() {
             return {
                 confirmed:false
            };
        },
        computed: {
           title(){

                let marketRequest = this.marketNegotiation.getUserMarket().getMarketRequest();

                let title = marketRequest.trade_items.default[this.$root.config("trade_structure.outright.expiration_date")]+
                " "+marketRequest.trade_items.default[this.$root.config("trade_structure.outright.strike")];
                
                if(this.isBid)
                {
                    return this.confirmed ?  title : "Hit the bid?";   
                }else
                {
                    return this.confirmed ?  title : "Lift the offer?";   
                }
           },
           subtitle(){

            if(this.isBid){
                return this.confirmed ? "You Are Selling @ <span class='text-success'>" + this.marketNegotiation.bid +'</span>': "";
            }else
            {
                return this.confirmed ? "You Are Buying @ <span class='text-success'>" + this.marketNegotiation.offer +'</span>': "";
            }

           },
           btnText(){
                return this.isBid ? "Sell" : "Buy";
           }             
        },
        methods: {
            cancel(){
                this.$emit('close');
                this.confirmed = false;
            }
        },
        mounted() {

        }
    }
</script>