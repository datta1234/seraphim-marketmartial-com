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
        computed: {
            available: function() {
                let vals = {
                    bid: this.marketNegotiation.bid,
                    offer: this.marketNegotiation.offer,
                }
                let chosen_user_market = this.marketRequest.getChosenUserMarket();
                
                let available = this.parser(this.condition.value).filter(val => {
                    console.log("Ok maybe not", val, this.condition.value, this.parser(this.condition.value));
                    return val.applies_to.reduce((out, side) => {
                        if(vals[side] == null || vals[side] == 0) {
                            out = false;
                        }
                        return out;
                    }, true);
                });
                let found = available.find(x => {
                    return x.value == this.defaults[this.condition.alias];
                });
                if(!found) {
                    if(available.length > 0) {
                        this.defaults[this.condition.alias] = available[0].value;
                        this.$emit('change', available[0]);
                    } else {
                        this.defaults[this.condition.alias] = null;
                    }

                // @TODO - figure out how to get Condition to remove currently removes from UserMarketNegotiation but bug on view with
                //          dismissing the condition, aka. Data works but View is stuffed with this code
                } /*else {
                    let chosen_user_market = this.marketRequest.getChosenUserMarket();
                    let default_index = null;
                    if(available.length > 0 && chosen_user_market) {
                        available.forEach((value,index) => {
                            let amount_source = chosen_user_market.getLastNegotiation().getAmountSource(value.applies_to[0]);
                            if(amount_source && amount_source.is_my_org) {
                                default_index = index;
                            }
                        });
                        if(default_index !== null) {
                            this.defaults[this.condition.alias] = available[default_index].value;
                            this.$emit('change', available[default_index]);
                        } else {
                            this.defaults[this.condition.alias] = null;
                        }
                    }
                    
                }*/
                return available;
            }
        }
    }
</script>