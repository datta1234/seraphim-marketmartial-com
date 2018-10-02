<template>
    <b-row dusk="ibar-counter-history" class="negotiation-history">
        <b-col>
            <template v-for="item in history" v-if="item.id != ''">   
                <ibar-trade-request :market-negotiation="item" :selectable="isLast(item)" ></ibar-trade-request>
            </template>
        </b-col>
    </b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';

    export default {
        props: {
            history: {
                type: Array
            }
        },
        data() {
            return {
                
            }
        },
        computed: {
            lastItem() {
              return this.history[this.history.length - 1];
            },
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