<template>
    <div :dusk="'market-tab-'+marketRequest.id" style="width:100%">
        <div class="col col-12 text-center">
            <div class="row">
                <b-col class="market-tab-wrapper market-tab-ratio m-auto" :class="{ 'has-ratio': marketRequest.ratio, 'org-involvement': org_involved }">
                    <component class="market-tab p-3 mb-2 mt-2" :is="tabs[marketRequest.trade_structure]" :market-request="marketRequest" :no_cares="no_cares"></component>
                </b-col>
            </div>
        </div>
    </div>
</template>

<script>
    import UserMarketRequest from '../lib/UserMarketRequest';

    import Outright from './MarketTabs/Outright';
    import Risky from './MarketTabs/Risky';
    import Calendar from './MarketTabs/Calendar';
    import Fly from './MarketTabs/Fly';
    import OptionSwitch from './MarketTabs/OptionSwitch';
    import EFP from './MarketTabs/EFP';
    import Rolls from './MarketTabs/Rolls';
    import EFPSwitch from './MarketTabs/EFPSwitch';
    import VarSwap from './MarketTabs/VarSwap';
    export default {
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
            };
        },
        computed: {
            org_involved: function() {
                // this is not org involvement any more - its "is the market pending for others"
                return !this.marketRequest.attributes.market_open && (
                    this.marketRequest.attributes.involved
                    || this.marketRequest.myOrgInvolved()
                    || (
                        this.marketRequest.is_interest
                        && (
                            this.marketRequest.chosen_user_market == null
                            || (
                                this.marketRequest.chosen_user_market
                                && this.marketRequest.chosen_user_market.market_negotiations.length == 1
                            )
                        )
                    )
                );
            }
        },
        methods: {

        },
        mounted() {

        }
    }
</script>