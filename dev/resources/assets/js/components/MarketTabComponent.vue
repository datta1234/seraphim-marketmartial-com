<template>
    <div class="col col-12 text-center market-tab p-3 mb-2 mt-2" v-bind:class="marketState" @click="loadInteractionBar()">
        <div class="row justify-content-md-center">
            <div class="col col-6 market-tab-name market-tab-name">
                {{ market.strike }}    
            </div>
            <div class="col col-6 market-tab-state">
                
                <span v-if="market.state == 'request'">
                    <span class="">REQUEST</span>
                </span>
                <span v-else>
                    <span class="" v-bind:class="bidState">{{ market.bid }}</span> / <span class="" v-bind:class="offerState">{{ market.offer }}</span>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    const UserMarket = require('../lib/UserMarket');
    export default {
        props: {
            market: {
                type: UserMarket
            }
        },
        watch: {
            'market.bid': function (nV, oV) {
                // console.log("Market Bid Updated", oV, nV);
                this.bid_state = 'action';
            },
            'market.offer': function (nV, oV) {
                // console.log("Market Offer Updated", oV, nV);
                this.offer_state = 'action';
            }
        },
        data() {
            return {
                bid_state: '',
                offer_state: ''
            };
        },
        computed: {
            marketState: function() {
                return {
                    'market-request': this.market.state == 'request',
                    'market-alert': this.market.state == 'alert',
                    'market-confirm': this.market.state == 'confirm',
                }
            },
            bidState: function() {
                return {
                    'user-action': this.bid_state == 'action',
                }
            },
            offerState: function() {
                return {
                    'user-action': this.offer_state == 'action',
                }
            }
        },
        methods: {
            loadInteractionBar() {
                // console.log("load Bar");
            }
        },
        mounted() {

        }
    }
</script>