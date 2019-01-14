<template>
    <div dusk="ibar-negotiation-bar" class="ibar-negotiation-content">
        
        <component v-if="marketRequest != null" :is="layouts[marketRequest.trade_structure]" :market-request="marketRequest"></component>
        
        <b-row class="justify-content-md-center" v-if="$root.is_admin">
            <b-col cols="6">
                <b-button v-active-request class="w-100 mt-2" 
                 size="md" 
                 dusk="ibar-action-admin-pull" 
                 variant="danger"
                 @click="pullMarketRequest()">
                    Pull Market
                </b-button>
            </b-col>
        </b-row>

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
    import BarLayoutVarSwap from './BarLayouts/VarSwap';

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
            BarLayoutVarSwap
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
                    'EFP Switch': BarLayoutEFPSwitch,
                    'Rolls': BarLayoutRolls,
                    'Var Swap': BarLayoutVarSwap,
                }
            };
        },
        methods: {
            pullMarketRequest() {
                let do_pull = confirm("WARNING!\n\nAre you sure you wish to pull this Market Request?");
                if(do_pull) {
                    this.marketRequest.deactivate()
                    .then(response => {
                        this.$toasted.success("Market Request deactivated");
                    })
                    .catch(err => {
                        this.$toasted.error("Unable to deactivate Market Request!");
                    });
                }
            }
        },
        mounted() {
            
        }
    }
</script>