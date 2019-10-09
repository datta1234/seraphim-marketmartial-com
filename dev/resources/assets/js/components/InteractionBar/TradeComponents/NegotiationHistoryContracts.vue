<template>
    <b-row dusk="ibar-negotiation-history-contracts" class="ibar-negotiation-history-contracts">
        <b-col>
            <template v-for="item in history" v-if="item.id != ''">   
                <!-- && !item.is_killed"  -->
                <ibar-trade-request :market-negotiation="item" :selectable="isLast(item) && canInitiateTrade && !item.isTrading() && !item.isTraded() && !item.isNoTrade()" :is-current="isLast(item)"></ibar-trade-request>
            </template>
            <b-row v-if="message">
                <b-col cols="10" class="mt-2">
                    <p class="text-center ibar-message">
                        {{ message }}
                    </p>
                </b-col>
            </b-row>

        </b-col>
    </b-row>
</template>


<script>
    export default {
        data(){
         return {

         }
        },
        computed: {
            canInitiateTrade: function() {
                return this.lastItem.getUserMarket().getMarketRequest().canInitiateTrade();
            },
            // a computed getter
            lastItem() {
              return this.history[this.history.length - 1];
            },
        },
        props: {
            history: {
                type: Array
            },
            message:{
                type: String
            }
        },
        methods: {
            isLast(item)
            {
                return item.id == this.lastItem.id; 
            }
        },
        mounted() { 
        }
    }
</script>