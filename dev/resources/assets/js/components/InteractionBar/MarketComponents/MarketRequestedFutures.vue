<template>
    <b-row dusk="ibar-market-requested-futures" class="ibar-market-requested-futures market-requested-info">
        <b-col>
            <b-row align-v="end">
                <b-col cols="10">
                    <template v-for="(item, index) in tradings">
                        <b-row no-gutters v-if="columns.indexOf('future') != -1 && item.future">
                            <template v-if="isNearDated">
                                <b-col cols="9" class="info-col text-left">
                                    <span>
                                        Near Dated Future Ref: {{ splitValHelper(item.future, '&nbsp;', 3) }}
                                    </span>
                                </b-col>
                            </template>
                            <template v-else>
                                <b-col cols="3" class="info-col text-left">
                                    <span>
                                        {{ item.expiry }}
                                    </span>
                                </b-col>
                                <b-col cols="6" class="info-col text-left">
                                    <span>
                                        Future Ref: {{ splitValHelper(item.future, '&nbsp;', 3) }}
                                    </span>
                                </b-col>
                            </template>
                        </b-row>
                        <b-row no-gutters v-if="columns.indexOf('future_1') != -1 && item.future_1">
                            <b-col cols="3" class="info-col text-left">
                                <span>
                                    {{ item.expiry_1 }}
                                </span>
                            </b-col>
                            <b-col cols="6" class="info-col text-left">
                                <span>
                                    Future Ref: {{ splitValHelper(item.future_1, '&nbsp;', 3) }}
                                </span>
                            </b-col>
                        </b-row>
                        <b-row no-gutters v-if="columns.indexOf('future_2') != -1 && item.future_1">
                            <b-col cols="3" class="info-col text-left">
                                <span>
                                    {{ item.expiry_2 }}
                                </span>
                            </b-col>
                            <b-col cols="6" class="info-col text-left">
                                <span>
                                    Future Ref: {{ splitValHelper(item.future_2, '&nbsp;', 3) }}
                                </span>
                            </b-col>
                        </b-row>
                    </template>
                </b-col>
            </b-row>
            <b-row v-if="$root.is_admin">
                <b-col cols="12">
                    <span class="text-left admin-label is-interest" v-b-popover.hover.top="marketRequest.user">
                        {{ marketRequest.org }}
                    </span>
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
        isNearDated: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            keys: {}
        };
    },
    computed: {
        tradings: function() {
            let groups = [];
            let itt = 1;
            while(typeof this.keys['group_'+itt] !== 'undefined') {
                let row = {
                    group: this.keys['group_'+itt],
                    future: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.future],
                    future_1: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.future_1],
                    future_2: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.future_2],
                    expiry: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.expiration_date],
                    expiry_1: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.expiration_date_1],
                    expiry_2: this.marketRequest.trade_items[this.keys['group_'+itt]][this.keys.expiration_date_2],
                };
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