<template>
    <div dusk="market-tab" style="width:100%">
        <component :is="tabs[marketRequest.trade_structure]" :market-request="marketRequest"></component>
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
            marketRequest: {
                type: UserMarketRequest
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