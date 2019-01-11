<template>
    <b-row dusk="ibar-negotiation-history-market">
        <b-col>
            <b-row v-for="(item, index) in history" :key="index">            
                <b-col cols="10" v-if="$root.is_admin">
                    <b-row no-gutters>
                        <b-col cols="10" class="text-center admin-label" :class="{ 'is-my-org': item.is_interest }" v-b-popover.hover.top="item.user">
                            {{ item.org }}
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="2" v-if="$root.is_admin">
                    <p class="text-center mb-0">
                        <small>
                            <a href="" @click.stop.prevent="pullQuote(item)" style="color: red;font-weight: bold;">PULL</a>
                        </small>
                    </p>
                </b-col>
                <b-col cols="10" >
                    <b-row no-gutters v-if="item.is_interest && !$root.is_viewer">
                        <b-col cols="6" class="text-center" :class="{ 'text-my-org': item.is_maker }">
                            {{ getState(item) }}
                        </b-col>
                        <b-col cols="3" class="text-center">
                            <button v-active-request v-bind:class="{'btn-secondary': item.is_on_hold,'btn-primary': !item.is_on_hold}"
                                :disabled="item.is_on_hold"
                                class="w-100 btn btn-sm" 
                                @click="putQuoteOnHold(item)"
                                >
                                    HOLD
                            </button>
                        </b-col>
                        <b-col cols="3" class="text-center">
                            <b-btn v-active-request size="sm" class="w-100" variant="primary" @click="acceptQuote(item)">ACCEPT</b-btn>
                        </b-col>
                    </b-row>
                    <b-row no-gutters v-else-if="item.is_maker">
                        <b-col cols="3" class="text-center text-my-org">
                             {{ item.bid_qty ? item.bid_qty : "-"  }}
                        </b-col>
                        <b-col cols="3" class="text-center text-my-org">
                            {{ item.bid ? item.bid : "-"  }}
                        </b-col>
                        <b-col cols="3" class="text-center text-my-org">
                            {{ item.offer ? item.offer : "-"  }}
                        </b-col>
                        <b-col cols="3" class="text-center text-my-org">
                             {{ item.offer_qty ? item.offer_qty : "-"  }}
                        </b-col>
                    </b-row>
                    <b-row no-gutters v-else>
                        <b-col cols="12" class="text-center">
                            {{ getState(item) }}
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="2">
                    <p class="text-center">
                        <small>{{ item.time }}</small>
                    </p>
                </b-col>
            </b-row>
            <!-- Removed as specified in task MM-739 -->             
            <!-- <b-row class="justify-content-md-center" v-if="history.reduce((x,y) => x = y.is_interest, false)">
                <b-col class="mt-2">
                    <p>
                        <small>Quotes will default to HOLD after 30 minutes.</small>
                    </p>
                </b-col>
            </b-row> -->
            <!-- <b-row class="justify-content-md-center">
                <b-col class="mt-2">
                    <p class="ibar-message">
                        <small>To show a One Way, clear the quantity of the level NOT being shown only.</small>
                    </p>
                </b-col>
            </b-row> -->
            <b-row class="justify-content-md-center" v-if="vm_message">
                <b-col cols="10" class="mt-0">
                    <p class="text-center ibar-message">
                        {{ vm_message }}
                    </p>
                </b-col>
                <b-col cols="2" class="mt-0"></b-col>
            </b-row>
        </b-col>
    </b-row>
</template>

<script>
    import UserMarketRequest from '~/lib/UserMarketRequest';
    
    export default {
        props: {
            history: {
                type: Array
            },
            message:{
                type: String
            },
            marketRequest: {
                type: UserMarketRequest
            }
        },
        computed: {
            vm_message: function()
            {
                return (this.history_message == null) ? this.message : this.history_message;
            },
            spread_message: function() {
                let ts = this.marketRequest.trade_structure_slug;
                console.log("Trade Structure: ", ts);
                if(['efp', 'efp_switch', 'rolls'].indexOf(ts) > -1) {
                    return "POINT SPREAD";
                }
                return "VOL SPREAD";
            }
        },
        data() {
            return {
                history_message: null 
            };
        },
        methods: {
            pullQuote(item) {
                let do_pull = confirm("WARNING!\n\nAre you sure you wish to pull this quote?");
                if(do_pull) {
                    item.delete();
                }
            },
            getState(item) {
                if(item.bid_only) {
                    return "BID ONLY";
                }
                if(item.offer_only) {
                    return "OFFER ONLY";
                }
                if(item.vol_spread != null) {
                    return item.vol_spread+' '+this.spread_message;
                }
                return "";
            },
            putQuoteOnHold(quote) {
                if(!quote.is_on_hold) {

                    quote.putOnHold()
                    .then(response => {
                        
                        this.history_message = response.data.message;
                        //@TODO FIND out why this does not work.
                        quote.is_on_hold = true;
                        this.$emit("update-on-hold");

                    })
                    .catch(err => {
                        this.errors = err.errors;
                    });

          
                }
            },
            acceptQuote(quote){
                 quote.accept()
                    .then(response => {
                        this.history_message = response.data.message;
                        this.$emit("update-on-accept");
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