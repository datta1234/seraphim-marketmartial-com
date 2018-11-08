<template>
    <b-row dusk="ibar-market-requested" class="market-requested-info">
        <b-col>
            <b-row align-v="end">
                <b-col cols="10">
                    <b-row no-gutters v-for="(item, index) in tradings" :key="index">
                        <b-col cols="3" class="text-left" v-if="columns.indexOf('quantity') != -1">
                            <span v-if="item.is_stock">
                                ({{ formatRandQty(item.quantity, '&nbsp;', 3) }}m)
                            </span>
                            <span v-else>
                                ({{ splitValHelper(item.quantity, '&nbsp;', 3) }})
                            </span>
                        </b-col>
                        <b-col cols="3" class="text-left" v-if="columns.indexOf('expiration_date') != -1">
                            {{ item.expiry }}
                        </b-col>
                        <b-col cols="3" class="text-left" v-if="columns.indexOf('strike') != -1">
                            <span v-if="item.is_stock">
                                {{ formatRandQty(item.strike, '&nbsp;', 3) }}{{ (item.choice ? '&nbsp;ch' : '') }}
                            </span>
                            <span v-else>
                                {{ splitValHelper(item.strike, '&nbsp;', 3) }}{{ (item.choice ? '&nbsp;ch' : '') }}
                            </span>
                        </b-col>
                        <b-col cols="3" class="text-center" v-if="columns.indexOf('status') != -1">
                            {{ (item.choice ? ( item.vol ? item.vol : 'choice' )  : 'bid&nbsp;/&nbsp;offer') }}
                        </b-col>
                    </b-row>
                </b-col>
                <b-col cols="2">
                    <p class="pull-right m-0">{{ time }}</p>
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>
<script>
import UserMarketRequest from '~/lib/UserMarketRequest';

export default {
    props: {
        marketRequest: UserMarketRequest,
        columns: Array,
    },
    data() {
        return {
            keys: {}
        };
    },
    computed: {
        time: function() {
            if(this.marketRequest.chosen_user_market) {
                return this.marketRequest.chosen_user_market.updated_at.format("HH:mm");
            }
            return this.marketRequest.updated_at.format("HH:mm");
        },
        tradings: function() {
            let groups = [];
            let itt = 1;
            while(typeof this.keys['group_'+itt] !== 'undefined') {
                let row = {
                    group: this.keys['group_'+itt],
                    quantity: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.quantity],
                    expiry: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.expiration_date],
                    strike: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.strike],
                    is_stock: this.marketRequest.trade_items[this.keys['group_'+itt]].tradable.is_stock,
                    choice: this.marketRequest.trade_items[this.keys['group_'+itt]]['choice'], // this one is static
                    vol: null
                };
                if(this.marketRequest.chosen_user_market) {
                    let id = this.marketRequest.trade_items[this.keys['group_'+itt]].id;
                    let vol = this.marketRequest.chosen_user_market.volatilityForGroup(id);
                    if(vol && vol.value) {
                        row.vol = vol.value;
                    }
                }
                groups.push(row);
                itt++;
            }
            return groups;
        }
    },
    mounted() {
        this.keys = this.$root.config('trade_structure.'+this.marketRequest.trade_structure_slug);
    }
}
</script>