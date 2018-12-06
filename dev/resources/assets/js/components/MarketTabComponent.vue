<template>
    <div :dusk="'market-tab-'+marketRequest.id" style="width:100%">
        <div class="col col-12 text-center">
            <div class="row">
                <b-col class="market-tab-wrapper market-tab-ratio m-auto" :class="{ 'has-ratio': marketRequest.ratio }">
                    <component class="market-tab p-3 mb-2 mt-2" :is="tabs[marketRequest.trade_structure]" :market-request="marketRequest" :no_cares="no_cares"></component>
                </b-col>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../lib/EventBus.js';
    import UserMarketRequest from '../lib/UserMarketRequest';

    import MarketTabOutright from './MarketTabs/Outright';
    import MarketTabRisky from './MarketTabs/Risky';
    import MarketTabCalendar from './MarketTabs/Calendar';
    import MarketTabFly from './MarketTabs/Fly';
    import OptionSwitch from './MarketTabs/OptionSwitch';
    import EFP from './MarketTabs/EFP';
    import Rolls from './MarketTabs/Rolls';
    import EFPSwitch from './MarketTabs/EFPSwitch';
    import VarSwap from './MarketTabs/VarSwap';
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
            VarSwap,
        },
        props: {
            marketRequest: {
                type: UserMarketRequest
            },
            no_cares: {
                type: Array
            }
        },
        data() {
            return {
                tabs: {
                    'Outright': MarketTabOutright,
                    'Risky': MarketTabRisky,
                    'Calendar': MarketTabCalendar,
                    'Fly': MarketTabFly,
                    'Option Switch': OptionSwitch,
                    'EFP': EFP,
                    'Rolls': Rolls,
                    'EFP Switch': EFPSwitch,
                    'Var Swap': VarSwap,
                }
            };
        },
        computed: {
            
        },
        methods: {

        },
        mounted() {

        }
    }
</script>