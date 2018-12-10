<template>
    <div dusk="market-tab-fly"  class="market-tab-fly" v-bind:class="marketState" @click="loadInteractionBar()">
        <b-row class="justify-content-md-center" align-v="center">
            <b-col class="market-tab-name market-tab-name">
                <b-row no-gutters align-v="center" align-h="center">
                    <b-col v-if="marketRequest.market.is('singles')">
                        {{ marketRequest.trade_items.default.tradable.title }}&nbsp;
                    </b-col>
                    <b-col cols="auto">
                        FLY
                    </b-col>
                </b-row>
            </b-col>
            <b-col class="market-tab-state">
                <b-row align-v="center" align-h="center" class="h-100">
                    <b-col v-if="market_request_state_label != ''">
                        <span v-bind:class="{'user-action': market_request_state_label == 'SENT'}" class="">{{ market_request_state_label }}</span>
                    </b-col>
                    <b-col v-else>
                        <b-row>
                            <b-col>
                                <span v-if="choice_vol_1 != null">{{ choice_vol_1 }}</span>
                                <span v-else>&nbsp;</span>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <span class="" v-bind:class="bidState">{{ user_market_bid }}</span>&nbsp;/&nbsp;<span class="" v-bind:class="offerState">{{ user_market_offer }}</span>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <span v-if="choice_vol_2 != null">{{ choice_vol_2 }}</span>
                                <span v-else>&nbsp;</span>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    import { EventBus } from '~/lib/EventBus.js';
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import MarketTabMixin from '~/components/MarketTabMixin';
    
    export default {
        props: {
            marketRequest: {
                type: UserMarketRequest
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
            'choice_vol_1': function() {
                let group = this.$root.config("trade_structure.fly.group_1");
                let id = this.marketRequest.trade_items[group].id;
                if(this.marketRequest.chosen_user_market) {
                    let vol = this.marketRequest.chosen_user_market.volatilityForGroup(id);
                    if(vol && vol.value) {
                        return vol.value;
                    }
                }
                return null;
            },
            'choice_vol_2': function() {
                let group = this.$root.config("trade_structure.fly.group_3");
                let id = this.marketRequest.trade_items[group].id;
                if(this.marketRequest.chosen_user_market) {
                    let vol = this.marketRequest.chosen_user_market.volatilityForGroup(id);
                    if(vol && vol.value) {
                        return vol.value;
                    }
                }
                return null;
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