<template>
    <b-row dusk="ibar-apply-conditions">
        <b-col>
            <b-row>
                <b-col>
                    <b-form-checkbox v-model="show_options" dusk="ibar-apply-a-condition" name="apply-a-condition" :value="true" :unchecked-value="false"> Apply a condition</b-form-checkbox>
                </b-col>
            </b-row>
            <b-row v-if="show_options" class="text-center" role="tablist">
            
                <b-col v-for="(condition, c_group) in conditions" :key="c_group" cols="12" v-if="condition.hidden !== true && conditionDisplayed(condition)">
                    <b-btn @click="onToggleClick(condition, c_group)"
                            variant="default" 
                            size="sm" 
                            class="w-75 mt-2 ibar-condition" 
                            role="tab"
                            :class="shown_groups[c_group] ? 'collapsed' : null"
                            :aria-expanded="shown_groups[c_group] ? 'true' : 'false'"
                            >
                        {{ condition.title }}
                    </b-btn>
                    <b-collapse :id="'condition_'+c_group" 
                                v-model="shown_groups[c_group]"
                                class="w-75 ibar-condition-panel" 
                                accordion="conditions" 
                                role="tabpanel">
                        
                        <div class="ibar-condition-panel-content text-left" v-if="condition.component">
                            <component  :is="components[condition.component]" :market-negotiation="marketNegotiation"></component>
                        </div>
                        <div class="ibar-condition-panel-content text-left" v-else-if="condition.children && condition.children.length > 0">
                            <div v-for="(child, index) in condition.children" :key="index" v-if="child.hidden !== true && conditionDisplayed(child)">
                                <label class="title">{{ child.title }}</label>
                                <div class="content">
                                    <b-form-radio-group v-if="child.value.constructor === Array"
                                                        v-bind:options="child.value"
                                                        stacked
                                                        v-on:change="e => setCondition(child.alias, e, c_group)"
                                                        name="">
                                    </b-form-radio-group>
                                </div>
                            </div>
                        </div>

                        <div class="ibar-condition-panel-content text-left" v-else>
                            <b-form-radio-group v-if="condition.value.constructor === Array"
                                                v-bind:options="condition.value"
                                                stacked
                                                v-on:change="e => setCondition(condition.alias, e, c_group)"
                                                name="">
                            </b-form-radio-group>
                        </div>
                    </b-collapse>
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>
<script>
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import conditionPropose from './Conditions/Propose';
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
        name: 'ibar-apply-conditions',
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            },
            marketRequest: {
                type: UserMarketRequest
            }
        },
        components: {
            'condition-propose': conditionPropose
        },
        data() {
            return {
                shown_groups: [],
                show_options: false,
                conditions: this.$root.config('market_conditions'),
                components: {
                    'condition-propose': conditionPropose
                }
            };
        },
        watch: {
            marketNegotiation() {
                this.show_options = false;
            },
            show_options() {
                this.resetConditions();
                this.updateShownGroups();
            }
        },
        computed: {
            negotiation_stage() {
                return this.marketRequest.state();
            },
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

                let aliases = getAlias(this.conditions);
                return aliases;
            }
        },
        methods: {
            conditionDisplayed(cond){
                console.log(cond, this.negotiation_stage);
                if(typeof cond.only !== 'undefined') {
                    if(cond.only == this.negotiation_stage) {
                        return true;
                    }
                    return false;
                }
                return true;
            },
            onToggleClick(condition, group) {
                if(condition.children) {
                    condition.children.forEach(child => {
                        if(typeof child.value !== 'undefined') {
                            if(child.value.constructor == Boolean) {
                                this.setCondition(child.alias, child.value, group);
                            }
                            if(child.value.constructor == Array) {
                                this.setCondition(child.alias, child.value[0], group);
                            }
                        }
                    });
                } else {
                    if(typeof condition.value !== 'undefined') {
                        if(condition.value.constructor == Boolean) {
                            this.setCondition(condition.alias, condition.value, group);
                        }
                        if(condition.value.constructor == Array) {
                            this.setCondition(condition.alias, condition.value[0], group);
                        }
                    }
                }
            },
            updateShownGroups() {
                this.shown_groups = [];
                for(let k in this.condition_aliases) {
                    if(typeof this.shown_groups[this.condition_aliases[k].group] === 'undefined') {
                        this.shown_groups[this.condition_aliases[k].group] = false;
                    }
                    if(this.marketNegotiation[k] != this.condition_aliases[k].default) {
                        this.shown_groups[this.condition_aliases[k].group] = true;
                    }
                }
            },
            setCondition(alias, value, group) {
                this.resetConditions(group, value.ignores);
                switch(value.constructor) {
                    case Object:
                        this.marketNegotiation[alias] = value.value;
                        if(value.sets) {
                            value.sets.forEach(v => {
                                this.marketNegotiation[v.alias] = v.value;
                            });
                        }
                    break;
                    case Boolean:
                    default:
                        this.marketNegotiation[alias] = value;
                }
                this.updateShownGroups();
            },
            resetConditions(group, ignores) {
                for(let k in this.condition_aliases) {
                    if(group) {
                        if(group != this.condition_aliases[k].group) {
                            if(ignores) {
                                if(ignores.indexOf(k) == -1) {
                                    this.marketNegotiation[k] = this.condition_aliases[k].default;
                                }
                            } else {
                                this.marketNegotiation[k] = this.condition_aliases[k].default;
                            }
                        }    
                    } else {
                        if(ignores) {
                            if(ignores.indexOf(k) == -1) {
                                this.marketNegotiation[k] = this.condition_aliases[k].default;
                            }
                        } else {
                            this.marketNegotiation[k] = this.condition_aliases[k].default;
                        }
                    }
                }
            }
        },
        mounted() {
            this.$watch(() => {
                let val = "";
                for(let k in this.condition_aliases) {
                    val += this.marketNegotiation[k];
                }
                return val;
            }, () => {
                Vue.nextTick(() => {
                    this.updateShownGroups();
                });
            })
            this.updateShownGroups();
        }
    }
</script>