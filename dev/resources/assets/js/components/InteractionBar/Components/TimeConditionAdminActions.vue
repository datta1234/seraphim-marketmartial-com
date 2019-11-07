<template>
    <b-row dusk="ibar-time-cond-admin-action" class="active-cond-bar" v-if="!timedOut && marketNegotiation">
        <b-col cols="12" v-if="!marketNegotiation.isTimeoutLocked()">
            <b-button v-active-request class="admin-condition-btn"  
                      size="sm" 
                      dusk="ibar-condition-end" 
                      variant="primary" 
                      @click="alterTimer('end')">
                        End Timer
            </b-button>
            <b-button v-active-request class="float-right admin-condition-btn"  
                      size="sm" 
                      dusk="ibar-condition-reset" 
                      variant="primary" 
                      @click="alterTimer('reset')">
                        Reset Timer
            </b-button>
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
            timedOut: {
                type: Boolean,
                default: true
            }
        },
        methods: {
            alterTimer(option) {
                this.marketNegotiation.alterTradeAtBestTimer(option)
                .then(response => {
                    this.errors = [];
                })
                .catch(err => {
                    this.errors = err.errors;
                });
            }
        },
        mounted() {
        }
    }
</script>