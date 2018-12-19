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
                let available = this.parser(this.condition.value).filter(val => {
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
                }
                console.log("Checking this ==================================", available, this.defaults);
                return available;
            }
        }
    }
</script>