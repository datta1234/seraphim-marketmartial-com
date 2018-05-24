<template>
    <b-container fluid>
        
        <b-row class="mb-3">
            <b-col cols="10">
                <h3>{{ market.getParent().title }} {{ market.date }} {{ market.strike }}</h3>
            </b-col>
            <b-col cols="2">
                <p class="pull-right">10:00</p>
            </b-col>
        </b-row>
        
        <b-row class="mb-2" v-for="item in market.negotiations">            
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

        <b-row class="mb-3">
            <b-col cols="10">
                <b-form inline>
                    <b-form-input class="w-25" v-model="bid_qty" type="number" placeholder="Qty"></b-form-input>
                    <b-form-input class="w-25" v-model="bid" type="text" placeholder="Bid"></b-form-input>
                    <b-form-input class="w-25" v-model="offer" type="number" placeholder="Offer"></b-form-input>
                    <b-form-input class="w-25" v-model="offer_qty" type="text" placeholder="Qty"></b-form-input>
                </b-form>
            </b-col>
            <b-col cols="2">

            </b-col>
        </b-row>

        <b-row class="justify-content-md-center mb-1">
            <b-col>
                <b-button class="w-100" size="sm" variant="primary">Send</b-button>
            </b-col>
        </b-row>
        <b-row class="justify-content-md-center mb-5">
            <b-col>
                <b-button class="w-100" size="sm" variant="secondary">No Cares</b-button>
            </b-col>
        </b-row>

        <b-row class="mb-5">
            <b-col>
                <b-form-checkbox v-model="state_conditions" value="true" unchecked-value="false"> Apply a condition</b-form-checkbox>
            </b-col>
        </b-row>

        <b-row class="mb-2">
            <b-col>
                <b-form-checkbox v-model="state_premium_calc" value="true" unchecked-value="false"> Apply premium calculator</b-form-checkbox>
            </b-col>
        </b-row>

    </b-container>
</template>

<script>
    const UserMarket = require('../../lib/UserMarket');
    const MarketNegotiation = require('../../lib/MarketNegotiation');
    export default {
        props: {
            market: {
                type: UserMarket
            }
        },
        data() {
            return {
                bid: null,
                offer: null,
                bid_qty: 0,
                offer_qty: 0,

                state_conditions: false,
            };
        },
        computed: {

        },
        methods: {
            
        },
        mounted() {

        }
    }
</script>