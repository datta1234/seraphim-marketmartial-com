<template>
    <b-row dusk="ibar-proposal-active" class="active-cond-bar">
        <b-col cols="12" v-b-toggle="'condition-history-'+negotiation.id" class="text-center condition-toggle">
            <strong>Proposal (Private)</strong>
        </b-col>
        <b-col cols="12">
            <b-collapse :id="'condition-history-'+negotiation.id" class="mt-2">
                <counter-history :history="condition.history"></counter-history>
            </b-collapse>
        </b-col>
        <b-col cols="12" class="mb-1">
            <div class="cond-bar">
                <b-row id="cond-container" class="trade-popover" no-gutters>
                    <b-col cols="6">
                        <span>
                            Trade At {{ negotiation.bid+" / "+negotiation.offer }}
                        </span>
                    </b-col>
                    <b-col cols="2">
                        <span>
                            <a href="" @click.prevent.stop="doTrade">Trade</a>
                        </span>
                    </b-col>
                    <b-col cols="4">
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

    import CounterHistory from './CounterHistory';

    export default {
        props: {
            condition: {
                type: ActiveCondition
            },
        },
        components: {
            CounterHistory,
        },
        data() {
            return {
                counter_open: false
            }
        },
        computed: {
            negotiation() {
                return this.condition.condition;
            }
        },
        methods: {
            doTrade() {
                
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