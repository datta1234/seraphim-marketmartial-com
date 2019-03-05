<template>
    <b-row dusk="ibar-market-negotiation-market" class="ibar market-negotiation">
        <b-col>
            <b-row class="mb-3">
                <b-col cols="10">
                    <b-form inline>
                        <div class="w-25 p-1 m-auto">
                            <b-form-input v-active-request v-input-mask.number.decimal="{ precision: 2 }" class="w-100" v-model="volatility.value" :disabled="disabled" type="text" dusk="market-negotiation-vol" placeholder="Choice"></b-form-input>
                        </div>
                    </b-form>
                </b-col>
                <b-col cols="2">
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>
<script>
import UserMarket from '~/lib/UserMarket';
import UserMarketQuote from '~/lib/UserMarketQuote';
import UserMarketVolatility from '~/lib/UserMarketVolatility';

export default {
    props: {
        userMarket: {
            type: [UserMarket, UserMarketQuote],
            default: new UserMarket()
        },
        tradeGroup: Object,
        disabled: Boolean
    },
    watch: {
        'userMarket':function(nv, ov) {
            this.assignVolatility();
        }
    },
    data() {
        return {
            volatility: new UserMarketVolatility()
        }
    },
    methods: {
        assignVolatility() {
            let vol = this.userMarket.volatilityForGroup(this.tradeGroup.id);
            if(typeof vol != 'undefined') {
                this.volatility = vol;
            } else {
                this.volatility.group_id = this.tradeGroup.id;
                this.userMarket.addVolatility(this.volatility);
            }    
        }
    },
    mounted() {
        this.assignVolatility();
    }
}
</script>