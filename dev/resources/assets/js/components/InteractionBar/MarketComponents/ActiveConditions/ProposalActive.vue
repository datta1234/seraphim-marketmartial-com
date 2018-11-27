<template>
    <b-row dusk="ibar-proposal-active" class="active-cond-bar">
        <b-col cols="12" v-b-toggle="'condition-history-'+negotiation.id" class="text-center condition-toggle">
            <strong>Proposal (Private)</strong>
        </b-col>
        <b-col cols="12">
            <b-collapse :id="'condition-history-'+negotiation.id" class="mt-2" @shown="refreshTooltip()" @hidden="refreshTooltip()">
                <counter-history :history="condition.history"></counter-history>
            </b-collapse>
        </b-col>
        <b-col cols="12" class="mb-1">
            <div v-bind:class="[ isActive ? 'cond-bar-alert' : 'cond-bar-sent' ]">
                <b-row id="cond-container" class="trade-popover" no-gutters>
                    <b-col :cols=" isActive ? '6' : '12'">
                        <span>
                            Trade At {{ ( negotiation.bid ? negotiation.bid : '-' )+" / "+( negotiation.offer ? negotiation.offer : '-' ) }}
                        </span>
                    </b-col>
                    <b-col v-if="isActive" cols="6">
                        <span id="proposal-do-trade">
                            <a href="" @click.prevent.stop="doTrade">Trade</a>
                        </span>
                        <span>
                            <a href="" @click.prevent.stop="doReject">Reject</a>
                        </span>
                        <span id="proposal-counter">
                            <a href="" @click.prevent.stop="doCounter">Counter</a>
                        </span>
                    </b-col>
                </b-row>
            </div>
        </b-col>
        
        <ibar-trade-desired-quantity 
            ref="proposalTradePopover" 
            target="proposal-do-trade" 
            :market-negotiation="negotiation" 
            :open="trade_open" 
            :is-offer="null"
            @close="trade_open = false" 
            parent="cond-container">
        </ibar-trade-desired-quantity>

        <ibar-counter-negotiation 
            ref="proposalCounterPopover" 
            target="proposal-counter" 
            :market-negotiation="negotiation" 
            :open="counter_open"
            @close="counter_open = false" 
            parent="trade_app"
            placement="bottom"
            popover-class="popover-meet-in-middle">
        </ibar-counter-negotiation>

    </b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import ActiveCondition from '~/lib/ActiveCondition';
    import SentCondition from '~/lib/SentCondition';

    import CounterHistory from './CounterHistory';

    export default {
        props: {
            condition: {
                type: [ActiveCondition,SentCondition]
            },
            isActive: {
                type: Boolean,
                default: true,
            },
        },
        components: {
            CounterHistory,
        },
        data() {
            return {
                trade_open: false,
                counter_open: false
            }
        },
        computed: {
            negotiation() {
                return this.condition.condition;
            }
        },
        methods: {
            refreshTooltip() {
                console.log("Refreshing", this.$refs.proposalCounterPopover)
                this.$refs.proposalCounterPopover.$forceUpdate();
            },
            doTrade() {
                this.trade_open = true;
            },
            doCounter() {
                this.counter_open = true;
            },
            doReject() {
                this.negotiation.killNegotiation()
                .then(response => {
                    console.log(response);
                    this.errors = [];
                })
                .catch(err => {
                    this.errors = err.errors.errors;
                });
            },
        },
        mounted() {
        }
    }
</script>