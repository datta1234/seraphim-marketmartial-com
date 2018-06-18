<template>
    <b-container fluid dusk="ibar-negotiation-bar">
        
        <component v-if="marketRequest != null" :is="layouts[marketRequest.trade_structure]" :market-request="marketRequest"></component>

    </b-container>
</template>
<script>
    import { EventBus } from '../../lib/EventBus.js';
    import UserMarketRequest from'../../lib/UserMarketRequest';
    import UserMarketNegotiation from'../../lib/UserMarketNegotiation';

    import BarLayoutOutright from './BarLayouts/Outright';
    import BarLayoutRisky from './BarLayouts/Risky';
    import BarLayoutCalendar from './BarLayouts/Calendar';
    import BarLayoutFly from './BarLayouts/Fly';

    export default {
        components: {
            BarLayoutOutright,
            BarLayoutRisky,
            BarLayoutCalendar,
            BarLayoutFly,
        },
        props: {
            marketRequest: {
                type: UserMarketRequest
            }
        },
        data() {
            return {
                layouts: {
                    Outright: BarLayoutOutright,
                    Risky: BarLayoutRisky,
                    Calendar: BarLayoutCalendar,
                    Fly: BarLayoutFly,
                }
            };
        },
        mounted() {

        }
    }
</script>