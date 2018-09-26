<template>
    <div dusk="market-tab-outright"  class="col market-tab p-3 mb-2 mt-2" v-bind:class="marketState" @click="loadInteractionBar()">
        <div class="row justify-content-md-center">
            <div class="col market-tab-name market-tab-name">
                    {{ splitValHelper( marketRequest.trade_items.default[this.$root.config("trade_structure.outright.strike")], '&nbsp;', 3) }}  
            </div>
            <div class="col market-tab-state">
                
                <span v-if="market_request_state_label != ''">
                    <span v-bind:class="{'user-action': market_request_state_label == 'SENT'}" class="">{{ market_request_state_label }}</span>
                </span>
                <span v-else>
                    <span class="" v-bind:class="bidState">{{ user_market_bid }}</span>&nbsp;/&nbsp;<span class="" v-bind:class="offerState">{{ user_market_offer }}</span>
                </span>
            </div>
        </div>
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
                console.log('bid state', this.marketRequest);
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
                console.log('offer state', this.marketRequest);
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