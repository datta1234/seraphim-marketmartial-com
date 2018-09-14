<template>
    <b-row dusk="ibar-remove-conditions">
        <b-col cols="12" v-for="(group, key) in condition_groups" class="text-center">
            <label class="ibar-condition-remove-label" @click="removeConditionGroup(group)" v-if="group.title && groupIsSet(group)">
                {{ group.title }}&nbsp;&nbsp;<span class="remove">X</span>
            </label>
        </b-col>
    </b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    /*
        Sets the state of the following attributes on the 'marketNegotiation'
            cond_is_repeat_atw
            cond_fok_apply_bid
            cond_fok_spin
            cond_timeout
            cond_is_ocd
            cond_is_subject
            cond_buy_mid
            cond_buy_best
    */
    export default {
        name: 'ibar-remove-conditions',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            }
        },
        data() {
            return {
                conditions: this.$root.config('market_conditions')
            };
        },
        computed: {
            condition_aliases() {
                let getAlias = (list, group) => {
                    return list.reduce((a, v, i) => {
                        if(v.alias) {
                            a[v.alias] = {
                                default: v.default,
                                group: ( group ? group : i )
                            };
                        }
                        if(v.children) {
                            Object.assign(a, getAlias(v.children, ( group ? group : i )));
                        }
                        return a;
                    }, {});
                };
                return getAlias(this.conditions);
            },
            condition_groups() {
                let getSets = (item) => {
                    if(typeof item.value !== 'undefined') {
                        switch(item.value.constructor) {
                            case Array:
                                return item.value.reduce((s, v) => {
                                    let sets = getSets(v);
                                    if(sets != null) {
                                        if(s == null) {
                                            s = [];
                                        }
                                        s = s.concat(sets);
                                    }
                                    return s;
                                }, null);
                            break;
                            case Object:
                                return item.value.sets ? item.value.sets.reduce((a,v) => {
                                    a.push(v.alias);
                                    return a;
                                }, []) : null;
                        }
                    }
                    return null;
                };
                let getAlias = (list, grouping) => {
                    return list.reduce((a, v, i) => {
                        let group = ( grouping ? grouping : [i,v.title] );
                        if(v.alias) {
                            if(!a[group[0]]) {
                                a[group[0]] = {
                                    title: group[1],
                                    items: []
                                };
                            }
                            a[group[0]].items.push({
                                alias: v.alias,
                                default: v.default,
                                title: v.title,
                                sets: getSets(v),
                            });
                        }
                        if(v.children) {
                            Object.assign(a, getAlias(v.children, group));
                        }
                        return a;
                    }, {});
                };
                return getAlias(this.conditions);
            }
        },
        methods: {
            removeConditionGroup(group) {
                for(let i = 0, cond; cond = group.items[i]; i++) {
                    this.marketNegotiation[cond.alias] = cond.default;
                    if(cond.sets) {
                        for(let j = 0, set; set = cond.sets[j]; j++) {
                            this.marketNegotiation[set] = this.condition_aliases[set].default;
                        }
                    }
                }
            },
            groupIsSet(group) {
                for(let i = 0, cond; cond = group.items[i]; i++) {
                    if(this.marketNegotiation[cond.alias] != cond.default)
                    {
                        return true;
                    }
                }
                return false;
            }
        },
        mounted(){
            
        }
    }
</script>