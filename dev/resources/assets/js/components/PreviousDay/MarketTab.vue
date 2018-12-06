<template>
    <div :dusk="'previous-day-market-tab-'+marketRequest.id" style="width:100%">
        <div class="col col-12 text-center">
            <div class="row">
                <b-col class="market-tab-wrapper market-tab-ratio m-auto" :class="{ 'has-ratio': marketRequest.ratio }">
                    <component class="market-tab p-3 mb-2 mt-2" :is="tabs[marketRequest.trade_structure]" :market-request="marketRequest" :interactable="false"></component>
                </b-col>
            </div>
        </div>
    </div>
</template>
<script>
    import UserMarketRequest from '~/lib/UserMarketRequest.js';

    import Outright from '~/components/MarketTabs/Outright';
    import Risky from '~/components/MarketTabs/Risky';
    import Calendar from '~/components/MarketTabs/Calendar';
    import Fly from '~/components/MarketTabs/Fly';
    import OptionSwitch from '~/components/MarketTabs/OptionSwitch';
    import EFP from '~/components/MarketTabs/EFP';
    import Rolls from '~/components/MarketTabs/Rolls';
    import EFPSwitch from '~/components/MarketTabs/EFPSwitch';
    import VarSwap from '~/components/MarketTabs/VarSwap';

    export default {
        props: {
            marketRequest: {
                type: UserMarketRequest
            }
        },
        components: {
            Outright,
            Risky,
            Calendar,
            Fly,
            OptionSwitch,
            EFP,
            Rolls,
            EFPSwitch,
            VarSwap,
        },
        data() {
            return {
                tabs: {
                    'Outright': Outright,
                    'Risky': Risky,
                    'Calendar': Calendar,
                    'Fly': Fly,
                    'Option Switch': OptionSwitch,
                    'EFP': EFP,
                    'Rolls': Rolls,
                    'EFP Switch': EFPSwitch,
                    'Var Swap': VarSwap,
                }
            }
        },
        mounted() {

        }
    };
</script>