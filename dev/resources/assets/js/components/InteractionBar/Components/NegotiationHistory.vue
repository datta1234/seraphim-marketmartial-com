<template>
    <b-row dusk="ibar-negotiation-history">
        <b-col>
            <b-row v-for="item in history">            
                <b-col cols="10">
                    <b-row>
                        <b-col cols="3">{{ formatRandQty(item.bid_qty) }}</b-col>
                        <b-col cols="3">{{ item.bid }}</b-col>
                        <b-col cols="3">{{ item.offer }}</b-col>
                        <b-col cols="3">{{ formatRandQty(item.offer_qty) }}</b-col>
                    </b-row>
                </b-col>
                <b-col cols="2">
                    <p>{{ item.time }}</p>
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