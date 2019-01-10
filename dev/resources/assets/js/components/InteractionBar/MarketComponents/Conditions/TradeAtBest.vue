<template>
    <div>
        <b-form-radio-group
                            v-active-request
                            v-model="marketNegotiation[condition.alias]"
                            v-bind:options="available"
                            stacked
                            name="">
        </b-form-radio-group>
    </div>
</template>
<script>
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import { EventBus } from '~/lib/EventBus';
    export default {
        name: 'condition-propose',
        props: {
            marketRequest: {
                type: UserMarketRequest,
                default: null
            },
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            },
            condition: {
                default: null
            }
        },
        methods: {
            mySideAtValue(side, value) {
                let chosen_user_market = this.marketRequest.getChosenUserMarket();
                if(!chosen_user_market) {
                    return false;
                }
                let current_negotiation = chosen_user_market.getLastNegotiation();
                // if the value is the same as the current one
                if(current_negotiation[side] == value) {
                    // i can only apply it if i own that side
                    let source = current_negotiation.getAmountSource(side);
                    return source.is_my_org;
                } else {
                    // if the value is different, and i have improved it, i will be owning that side, so i can apply it
                    return side == "bid" ? value > current_negotiation.bid : value < current_negotiation.offer;
                }
                return false;
            }
        },
        computed: {
            available: function() {
                let vals = {
                    bid: this.marketNegotiation.bid,
                    offer: this.marketNegotiation.offer,
                }
                let chosen_user_market = this.marketRequest.getChosenUserMarket();
                
                let available = this.condition.value.filter(val => {
                    return val.applies_to.reduce((out, side) => {
                        if(vals[side] == null || vals[side] == 0 || !this.mySideAtValue(side, vals[side])) {
                            out = false;
                        }
                        return out;
                    }, true);
                });

                if(available.length > 0) {
                    this.marketNegotiation[this.condition.alias] = available[0].value;
                } else {
                    this.marketNegotiation[this.condition.alias] = null;
                }
                console.log(available);

                return available;
            }
        },
        mounted() {
            console.log(this.condition);
        }
    }
</script>