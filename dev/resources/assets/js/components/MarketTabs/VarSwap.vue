<template>
    <div dusk="market-tab-var_wap"  class="market-tab-var_wap" v-bind:class="marketState" @click="loadInteractionBar()">
        <b-row class="justify-content-md-center">
            <b-col class="market-tab-name market-tab-name">
                <b-row no-gutters align-v="center" align-h="center">
                    <b-col cols="auto">
                        <div class="market-tab-strikes">
                            VAR Swap
                        </div>
                    </b-col>
                </b-row>
            </b-col>
            <b-col class="market-tab-state">
                <b-row align-v="center" align-h="center" class="h-100" v-if="market_request_state_label != ''">
                    <b-col>
                        <span v-bind:class="{'user-action': market_request_state_label == 'SENT'}" class="">{{ market_request_state_label }}</span>
                    </b-col>
                </b-row>
                <b-row align-v="center" align-h="center" class="h-100" v-else>
                    <b-col cols="12">
                        <span class="" v-bind:class="bidState">{{ user_market_bid }}</span>&nbsp;/&nbsp;<span class="" v-bind:class="offerState">
                            {{ user_market_offer }}
                        </span>
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
            tradable_1: function() {
                let group = this.$root.config("trade_structure.var_swap.group_1");
                return this.marketRequest.trade_items[group].tradable.title;
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