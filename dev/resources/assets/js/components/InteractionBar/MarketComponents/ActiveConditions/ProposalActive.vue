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
                        <span>
                            <a href="" @click.prevent.stop="doCounter">Counter</a>
                        </span>
                    </b-col>
                </b-row>
            </div>
        </b-col>
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

            }
        },
        methods: {
            doTrade() {
                
            },
            doCounter() {

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