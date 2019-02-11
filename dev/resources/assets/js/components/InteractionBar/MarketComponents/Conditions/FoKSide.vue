<template>
    <div>
        <b-form-radio-group
                            v-active-request
                            v-model="defaults[condition.alias]"
                            v-bind:options="available"
                            stacked
                            v-on:change="e => $emit('change', e)"
                            name="">
        </b-form-radio-group>
    </div>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import { EventBus } from '~/lib/EventBus';

    export default {
        name: 'condition-fok-side',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            },
            condition: {
                default: null
            },
            parser: {
                type: Function
            },
            defaults: {
                default: null
            },
            marketRequest: {
                type: UserMarketRequest
            }
        },
        data() {
            return {
                default: Object.assign({}, this.defaults[this.condition.alias])
            }
        },
        methods: {
            mySideAtValue(side, value) {
                let chosen_user_market = this.marketRequest.getChosenUserMarket();
                if(!chosen_user_market) {
                    return true;
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
                
                let available = this.parser(this.condition.value).filter(val => {
                    return val.applies_to.reduce((out, side) => {
                        if(vals[side] == null || vals[side] == 0 || !this.mySideAtValue(side, vals[side])) {
                            out = false;
                        }
                        return out;
                    }, true);
                });
                
                let foundIndex = available.findIndex(x => {
                    return this.defaults[this.condition.alias] && x.value.value === this.defaults[this.condition.alias].value;
                });

                // if its not found in the available list
                if(foundIndex === -1) {
                    // set it to the item thats first if there are some
                    if(available.length > 0) {
                        this.defaults[this.condition.alias] = available[0].value;
                        this.$emit('change', available[0]);
                    } else {
                        // default to null
                        this.defaults[this.condition.alias] = null;
                        this.marketNegotiation[this.condition.alias] = null;
                        EventBus.$emit('errorConditions', "Please improve the bid or the offer before applying an FoK.");
                        this.$emit('reset');
                    }
                } else {
                    // set to the default found value
                    this.defaults[this.condition.alias] = available[foundIndex].value;
                    this.$emit('change', available[foundIndex]);
                }
                return available;
            }
        }
    }
</script>