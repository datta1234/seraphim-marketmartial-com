<template>
    <div dusk="market-tab-efp-switch"  class="market-tab-efp-switch" v-bind:class="marketState" @click="loadInteractionBar()">
        <b-row class="justify-content-md-center">
            <b-col class="market-tab-name market-tab-name">
                <b-row no-gutters align-v="center" align-h="center">
                    <b-col cols="auto">
                        <div class="market-tab-strikes">
                            <span v-html="tradable_1"></span>
                            <br>
                            <span v-html="tradable_2"></span>
                        </div>
                    </b-col>
                    <b-col cols="auto">
                        <div class="market-tab-strikes pl-1">
                            <span v-html="expiry_1"></span>
                            <br>
                            <span v-html="expiry_2"></span>
                        </div>
                    </b-col>
                </b-row>
            </b-col>
            <b-col class="market-tab-state">
                <b-row align-v="center" align-h="center" class="h-100">
                    <b-col v-if="market_request_state_label != ''">
                        <span v-bind:class="{'user-action': market_request_state_label == 'SENT'}" class="">{{ market_request_state_label }}</span>
                    </b-col>
                    <b-col v-else>
                        <span class="" v-bind:class="bidState">{{ user_market_bid }}</span>&nbsp;/&nbsp;<span class="" v-bind:class="offerState">{{ user_market_offer }}</span>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import { EventBus } from '../../lib/EventBus.js';
    import UserMarketRequest from '../../lib/UserMarketRequest';
    import MarketTabMixin from '../MarketTabMixin';
    
    export default {
        props: {
            marketRequest: {
                type: UserMarketRequest
            },
             no_cares: {
                type: Array
            }
        },
        watch: {
            'marketRequest': {
                handler: function(nV, oV) {
                    this.calcMarketState();
                },
                deep: true
            }
        },
        mixins: [MarketTabMixin],
        data() {
            return {
                user_market: null,
                isActive: false,
            };
        },
        computed: {
            tradable_1: function() {
                let group = this.$root.config("trade_structure.efp_switch.group_1");
                return this.marketRequest.trade_items[group].tradable.title;
            },
            tradable_2: function() {
                let group = this.$root.config("trade_structure.efp_switch.group_2");
                return this.marketRequest.trade_items[group].tradable.title;
            },
            expiry_1: function() {
                let group = this.$root.config("trade_structure.efp_switch.group_1");
                let exp = this.$root.config("trade_structure.efp_switch.expiration_date");
                return this.marketRequest.trade_items[group][exp];
            },
            expiry_2: function() {
                let group = this.$root.config("trade_structure.efp_switch.group_2");
                let exp = this.$root.config("trade_structure.efp_switch.expiration_date");
                return this.marketRequest.trade_items[group][exp];
            },
            marketState: function() {
                return {
                    'trade-negotiation-open':  this.market_request_state == 'trade-negotiation-open',
                    'trade-negotiation-pending':  this.market_request_state == 'trade-negotiation-pending',
                    'negotiation-vol-pending':  this.market_request_state == 'negotiation-vol-pending',
                    'negotiation-vol': this.market_request_state == 'negotiation-vol',
                    'market-request-grey': this.market_request_state == 'request-grey',
                    'market-request': !this.marketRequest.is_interest  && this.no_cares.indexOf(this.marketRequest.id) == -1 && this.market_request_state == 'request',
                    'market-request-vol': this.market_request_state == 'request-vol',
                    'market-alert': this.marketRequest.attributes.action_needed,
                    'market-confirm': this.market_request_state == 'confirm',
                    'active': this.isActive,
                }
            },
            bidState: function() {
                if(this.marketRequest.chosen_user_market)
                {
                    return this.getStateClass(this.current_user_market_negotiation,'bid');
                }else
                {
                    return {
                       'user-action': this.marketRequest.attributes.bid_state == 'action',
                    }   
                }
               
            },
            offerState: function() {
                if(this.marketRequest.chosen_user_market)
                {
                    return this.getStateClass(this.current_user_market_negotiation,'offer');
                }else
                {
                    return {
                       'user-action': this.marketRequest.attributes.offer_state == 'action',
                    }   
                }
            }
        },
        methods: {
            loadInteractionBar() {
                this.toggleActionTaken();
                this.isActive = true;
                EventBus.$emit('toggleSidebar', 'interaction', true, this.marketRequest);
            }
        },
        mounted() {
            // initial setup of states
            this.calcMarketState();

            EventBus.$on('interactionClose', (marketRequest) => {
                this.isActive = false;
            });
            EventBus.$on('interactionChange', (marketRequest) => {
                if(this.marketRequest.id !== marketRequest.id) {
                    this.isActive = false;
                }else
                {
                    this.isActive = true;
                }
            });
        }
    }
</script>