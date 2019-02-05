<template>
    <b-row dusk="ibar-remove-conditions">
        <b-col cols="12" v-for="(group, key) in condition_groups" :key="key" class="text-center">
            <label class="ibar-condition-remove-label" @click="removeConditionGroup(group)" v-if="getTitle(group) && groupIsSet(group)">
                {{ getTitle(group) }}&nbsp;&nbsp;<span v-active-request class="remove">X</span>
            </label>
        </b-col>
    </b-row>
</template>
<script>
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import { EventBus } from '~/lib/EventBus';
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
                let getValueTitles = (value, list, title) => {
                    list = list ? list : {};
                    title = title ? title : null;
                    if(typeof value !== 'undefined' && value !== null)
                    switch(value.constructor) {
                        case Array:
                            value.forEach(x => {
                                list = Object.assign(list, getValueTitles(x, list, title));
                            });
                        break;
                        case Object:
                            if(value.title) {
                                title = value.title;
                            }
                            list = Object.assign(list, getValueTitles(value.value, list, title));
                        break;
                        default: 
                            list[String(value)] = title;
                    }
                    return list;
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
                                titles: getValueTitles(v.value)
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
                EventBus.$emit('resetConditions');
            },
            groupIsSet(group) {
                for(let i = 0, cond; cond = group.items[i]; i++) {
                    if(this.marketNegotiation[cond.alias] != cond.default)
                    {
                        return true;
                    }
                }
                return false;
            },
            getTitle(group) {
                console.log("Cond Group: ", group);
                let title = "";
                for(let i = 0, cond; cond = group.items[i]; i++) {
                    if(this.marketNegotiation[cond.alias] != cond.default)
                    {
                        if(cond.titles && cond.titles[String(this.marketNegotiation[cond.alias])] != null) {
                            title += cond.titles[String(this.marketNegotiation[cond.alias])];
                        }
                    }
                }
                return title == "" ? group.title : title;
            }
        },
        mounted() {
        }
    }
</script>