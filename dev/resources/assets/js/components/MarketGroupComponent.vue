<template>
    <div dusk="market-group" class="user-market">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-12">
                    <h2 class="text-center market-group-title">{{ market.title }}</h2> 
                </div>
                <div class="user-market-group col-12">

                    <!-- Date collection section -->
                    <div class="row mt-3 pr-3 pl-3" v-for="date in market_date_groups_order">
                        <div class="col-12">
                            <p class="mb-1">{{ date }}</p>
                        </div>
                        <market-tab :market-request="m_req" v-for="m_req in market_date_groups[date]" :no_cares="no_cares"></market-tab>
                    </div><!-- END Date collection section -->
                    
                </div>
                <div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Market from '../lib/Market';
    import moment from 'moment';
    

    export default {
        props: {
            'market': {
                type: Market
            },
            'no_cares':{
                type: Array
            }
        },
        watch: {
            'market.market_requests': function (nV, oV) {
                this.market_date_groups = this.mapMarketRequestGroups(nV);
                this.market_date_groups_order = this.sortMarketRequestGroups(this.market_date_groups);
            }
        },
        data() {
            return {
                market_date_groups: {},
                market_date_groups_order: []
            };
        },
        methods: {
            mapMarketRequestGroups: function(markets) {
                // map markets to dates
                return markets.reduce((x,y) => {
                    let date = y.trade_items.default ? y.trade_items.default[this.$root.config("trade_structure.outright.expiration_date")] : '';
                    if(!x[date]) { 
                        x[date] = [];
                    }
                    x[date].push(y);
                    return x;
                }, {});
            },
            sortMarketRequestGroups: function(unsorted_date_groups) {
                let dates = [];
                Object.keys(unsorted_date_groups).forEach( (date) => {
                    dates.push(date);
                });

                if(dates.length > 0) {
                    for(let i = 0; i < dates.length - 1; i++) {
                        for(let j = 0; j < dates.length - i - 1; j++) {
                            if( moment(dates[j+1],'MMMYY').isBefore(moment(dates[j],'MMMYY')) ) {
                                let temp = dates[j];
                                dates[j] = dates[j+1];
                                dates[j+1] = temp;
                            }
                        }
                    }
                }
                return dates;
            }
        },
        mounted() {
            this.market_date_groups = this.mapMarketRequestGroups(this.market.market_requests);
            console.log(this.market_date_groups);
        }
    }
</script>