<template>
    <div dusk="market-tab-efp_switch" class="col col-12 text-center">
        <div class="row">
            <div class="col market-tab p-3 mb-2 mt-2" v-bind:class="marketState" @click="loadInteractionBar()">
                <div class="row justify-content-md-center">
                    <div class="col col-6 market-tab-name market-tab-name">
                        {{ marketRequest.trade_items.default["Strike"] }}    
                    </div>
                    <div class="col col-6 market-tab-state">
                        
                        <span v-if="market_request_state_label != ''">
                            <span class="">{{ market_request_state_label }}</span>
                        </span>
                        <span v-else>
                            <span class="" v-bind:class="bidState">{{ user_market_bid }}</span> / <span class="" v-bind:class="offerState">{{ user_market_offer }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../../lib/EventBus.js';
    import UserMarketRequest from '../../lib/UserMarketRequest';
    export default {
        props: {
            marketRequest: {
                type: UserMarketRequest
            }
        },
        watch: {
            'marketRequest.attributes.state': function(nV, oV) {
                console.log('updated: marketRequest.user_markets');
                this.calcMarketState();
            },
            'marketRequest.user_markets': function(nV, oV) {
                console.log('updated: marketRequest.user_markets');
                this.calcMarketState();
            },
            'marketRequest.chosen_user_market': function(nV, oV) {
                console.log('updated: marketRequest._chosen_user_market');
                this.calcMarketState();
            }
        },
        data() {
            return {
                user_market: null,
                current_negotiation: null,
                market_request_state: '',
                market_request_state_label: '',

                user_market_bid: null,
                user_market_offer: null,
            };
        },
        computed: {
            marketState: function() {
                return {
                    'market-request-grey': this.market_request_state == 'request-grey',
                    'market-request': this.market_request_state == 'request',
                    'market-alert': this.market_request_state == 'alert',
                    'market-confirm': this.market_request_state == 'confirm',
                }
            },
            bidState: function() {
                return {
                    'user-action': this.marketRequest.attributes.bid_state == 'action',
                }
            },
            offerState: function() {
                return {
                    'user-action': this.marketRequest.attributes.offer_state == 'action',
                }
            }
        },
        methods: {
            loadInteractionBar() {
                console.log("load Bar");
                EventBus.$emit('toggleSidebar', 'interaction', true, this.marketRequest);
            },
            calcMarketState() {
                // set new refs
                this.user_market = this.marketRequest.getChosenUserMarket();
                this.current_negotiation = this.user_market ? this.user_market.getCurrentNegotiation() : null;
                this.user_market_bid = this.current_negotiation ? this.current_negotiation.bid : null;
                this.user_market_offer = this.current_negotiation ? this.current_negotiation.offer : null;
                
                // run tests
                // TODO: add logic for if current user then "SENT"
                switch(this.marketRequest.attributes.state) {
                    case "vol-spread":
                        this.market_request_state = '';
                        this.market_request_state_label = this.marketRequest.attributes.vol_spread+" VOL SPREAD";
                    break;
                    case "vol-spread-alert":
                        this.market_request_state = 'alert';
                        this.market_request_state_label = this.marketRequest.attributes.vol_spread+" VOL SPREAD";
                    break;
                    case "request":
                        this.market_request_state = 'request';
                        this.market_request_state_label = "REQUEST";
                    break;
                    case "request-grey":
                        this.market_request_state = 'request-grey';
                        this.market_request_state_label = "REQUEST";
                    break;
                    case "alert":
                        this.market_request_state = 'alert';
                        this.market_request_state_label = "";
                    break;
                    case "confirm":
                        this.market_request_state = 'confirm';
                        this.market_request_state_label = "";
                    break;
                    case "sent":
                        this.market_request_state = 'sent';
                        this.market_request_state_label = "SENT";
                    break;
                    default:
                        this.market_request_state = '';
                        this.market_request_state_label = '';
                }
            }
        },
        mounted() {
            // initial setup of states
            this.calcMarketState();
        }
    }
</script>