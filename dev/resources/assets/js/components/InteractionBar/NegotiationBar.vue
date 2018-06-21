<template>
    <div dusk="ibar-negotiation-bar">
        
        <component v-if="marketRequest != null" :is="layouts[marketRequest.trade_structure]" :market-request="marketRequest"></component>

    </div>
</template>
<script>
    import { EventBus } from '../../lib/EventBus.js';
    import UserMarketRequest from'../../lib/UserMarketRequest';
    import UserMarketNegotiation from'../../lib/UserMarketNegotiation';

    import BarLayoutOutright from './BarLayouts/Outright';
    import BarLayoutRisky from './BarLayouts/Risky';
    import BarLayoutCalendar from './BarLayouts/Calendar';
    import BarLayoutFly from './BarLayouts/Fly';
    import BarLayoutOptionSwitch from './BarLayouts/OptionSwitch';

    import BarLayoutEFP from './BarLayouts/EFP';
    import BarLayoutEFPSwitch from './BarLayouts/EFPSwitch';
    import BarLayoutRolls from './BarLayouts/Rolls';

    export default {
        components: {
            BarLayoutOutright,
            BarLayoutRisky,
            BarLayoutCalendar,
            BarLayoutFly,
            BarLayoutOptionSwitch,
            BarLayoutEFP,
            BarLayoutEFPSwitch,
            BarLayoutRolls,
        },
        props: {
            marketRequest: {
                type: UserMarketRequest
            }
        },
        data() {
            return {
                layouts: {
                    'Outright': BarLayoutOutright,
                    'Risky': BarLayoutRisky,
                    'Calendar': BarLayoutCalendar,
                    'Fly': BarLayoutFly,
                    'Option Switch': BarLayoutOptionSwitch,
                    'EFP': BarLayoutEFP,
                    'EFPSwitch': BarLayoutEFPSwitch,
                    'Rolls': BarLayoutRolls,
                }
            };
        },
        mounted() {
            
        }
    }
</script>