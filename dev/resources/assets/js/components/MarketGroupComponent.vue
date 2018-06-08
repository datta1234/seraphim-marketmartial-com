<template>
    <div dusk="market-group" class="user-market">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-12">
                    <h2 class="text-center">{{ market.title }}</h2> 
                </div>
                <div class="user-market-group col-12">

                    <!-- Date collection section -->
                    <div class="row mt-3 pr-3 pl-3" v-for="(m_reqs, exp_date) in market_date_groups">
                        <div class="col-12">
                            <p class="mb-1">{{ exp_date }}</p>
                        </div>
                        <market-tab :market-request="m_req" v-for="m_req in m_reqs"></market-tab>
                    </div><!-- END Date collection section -->
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const Market = require('../lib/Market')
    export default {
        props: {
            'market': {
                type: Market
            }
        },
        watch: {
            'market.market_requests': function (nV, oV) {
                // console.log("Markets Updated", oV, nV);
                this.market_date_groups = this.mapMarketRequestGroups(nV);
            }
        },
        data() {
            return {
                market_date_groups: {}
            };
        },
        methods: {
            mapMarketRequestGroups: function(markets) {
                // map markets to dates
                return markets.reduce((x,y) => {
                    let date = y.attributes.expiration_date.format("MMM D");  
                    if(!x[date]) { 
                        x[date] = [];
                    }
                    x[date].push(y);
                    return x;
                }, {});
            }
        },
        mounted() {
            this.market_date_groups = this.mapMarketRequestGroups(this.market.market_requests);
            console.log(this.market_date_groups);
        }
    }
</script>