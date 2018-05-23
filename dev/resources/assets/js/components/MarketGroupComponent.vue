<template>
    <div class="user-market">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center">{{ derivativeMarket.title }}</h2> 
                </div>
                <div class="user-market-group col-12">

                    <!-- Date collection section -->
                    <div class="row mt-3 pr-3 pl-3" v-for="(markets, date) in market_date_groups">
                        <div class="col-12">
                            <p class="mb-1">{{ date }}</p>
                        </div>
                        <market-tab :market="market" v-for="market in markets"></market-tab>
                    </div><!-- END Date collection section -->
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const DerivativeMarket = require('../lib/DerivativeMarket')
    export default {
        props: {
            'derivativeMarket': {
                type: DerivativeMarket
            }
        },
        watch: {
            'derivativeMarket.markets': function (nV, oV) {
                // console.log("Markets Updated", oV, nV);
                this.market_date_groups = this.mapMarketGroups(nV);
            }
        },
        data() {
            return {
                market_date_groups: {}
            };
        },
        methods: {
            mapMarketGroups: function(markets) {
                // map markets to dates
                return markets.reduce((x,y) => {
                    if(!x[y.date]) { 
                        x[y.date] = [];
                    }
                    x[y.date].push(y);
                    return x;
                }, {});
            }
        },
        mounted() {
            this.market_date_groups = this.mapMarketGroups(this.derivativeMarket.markets);
        }
    }
</script>