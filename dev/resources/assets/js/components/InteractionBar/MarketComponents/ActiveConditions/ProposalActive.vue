<template>
    <b-row dusk="ibar-proposal-active" class="active-cond-bar">
        <b-col cols="12">
            <div class="text-center">
                <strong>Proposal (Private)</strong>
            </div>
        </b-col>
        <b-col cols="12" class="mb-1">
            <div class="cond-bar">
                <b-row id="cond-container" class="trade-popover" no-gutters>
                    <b-col cols="6">
                        <span>
                            Trade At {{ marketNegotiation.bid+" / "+marketNegotiation.offer }}
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
            :market-negotiation="marketNegotiation" 
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

    export default {
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation
            },
        },
        data() {
            return {
                counter_open: false
            }
        },
        methods: {
            doTrade() {
                
            },
            doCounter() {
                this.counter_open = true;
            },
            doReject() {
                this.marketNegotiation.killNegotiation()
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