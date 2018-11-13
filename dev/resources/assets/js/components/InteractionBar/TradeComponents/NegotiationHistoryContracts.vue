<template>
    <b-row dusk="ibar-negotiation-history-contracts" class="ibar-negotiation-history-contracts">
        <b-col>
            <template v-for="item in history" v-if="item.id != ''">   
                <ibar-trade-request :market-negotiation="item" :selectable="isLast(item) && !item.is_killed" ></ibar-trade-request>
            </template>
            <b-row class="justify-content-md-center" v-if="message">
                <b-col cols="10" class="mt-2">
                    <p class="text-center">
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