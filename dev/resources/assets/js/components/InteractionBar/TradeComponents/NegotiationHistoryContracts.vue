<template>
    <b-row dusk="ibar-negotiation-history-contracts">
        <b-col>
            <b-row v-for="item in history">            
                <b-col cols="10">
                    <b-row>
                        <b-col cols="3" class="text-center">{{ formatRandQty(item.bid_qty) }}</b-col>
                        <b-col cols="3" class="text-center">{{ item.bid }}</b-col>
                        <b-col cols="3" class="text-center">{{ item.offer }}</b-col>
                        <b-col cols="3" class="text-center">{{ formatRandQty(item.offer_qty) }}</b-col>
                    </b-row>
                </b-col>
                <b-col cols="2">
                    <p class="text-center">{{ item.time }}</p>
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>

<script>
    export default {
        props: {
            history: {
                type: Array
            }
        },
        mounted() {

        }
    }
</script>