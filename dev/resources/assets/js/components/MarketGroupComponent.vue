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
                        <component :is="tabs[m_req.trade_structure]" :market-request="m_req" v-for="m_req in m_reqs"></component>
                    </div><!-- END Date collection section -->
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Market from '../lib/Market';

    import MarketTabOutright from './MarketTabs/Outright';
    import MarketTabRisky from './MarketTabs/Risky';
    import MarketTabCalendar from './MarketTabs/Calendar';
    import MarketTabFly from './MarketTabs/Fly';
    import OptionSwitch from './MarketTabs/OptionSwitch';
    import EFP from './MarketTabs/EFP';
    import Rolls from './MarketTabs/Rolls';
    import EFPSwitch from './MarketTabs/EFPSwitch';

    export default {
        components: {
            MarketTabOutright,
            MarketTabRisky,
            MarketTabCalendar,
            MarketTabFly,
            OptionSwitch,
            EFP,
            Rolls,
            EFPSwitch,
        },
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
                market_date_groups: {},
                tabs: {
                    'Outright': MarketTabOutright,
                    'Risky': MarketTabRisky,
                    'Calendar': MarketTabCalendar,
                    'Fly': MarketTabFly,
                    'Option Switch': OptionSwitch,
                    'EFP': EFP,
                    'Rolls': Rolls,
                    'EFP Switch': EFPSwitch,
                }
            };
        },
        methods: {
            mapMarketRequestGroups: function(markets) {
                // map markets to dates
                return markets.reduce((x,y) => {
                    let date = y.trade_items.default ? y.trade_items.default["Expiration Date"] : '';
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