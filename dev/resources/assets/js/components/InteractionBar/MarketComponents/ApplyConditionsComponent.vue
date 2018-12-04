<template>
    <b-row dusk="ibar-apply-conditions">
        <b-col>
            <b-row>
                <b-col>
                    <b-form-checkbox v-active-request v-model="show_options" dusk="ibar-apply-a-condition" name="apply-a-condition" :value="true" :unchecked-value="false"> Apply a condition</b-form-checkbox>
                </b-col>
            </b-row>
            <b-row v-if="show_options" class="text-center" role="tablist">
            
                <b-col v-for="(condition, c_group) in conditions" :key="c_group" cols="12" v-if="condition.hidden !== true && conditionDisplayed(condition)">
                    <b-btn @click="onToggleClick(condition, c_group)"
                            v-active-request
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
                            <component  v-if="shown_groups[c_group]" :is="components[condition.component]" :condition="condition" :market-negotiation="marketNegotiation"></component>
                        </div>
                        <div class="ibar-condition-panel-content text-left" v-else-if="condition.children && condition.children.length > 0">
                            <div v-for="(child, index) in condition.children" :key="index" v-if="child.hidden !== true && conditionDisplayed(child)">
                                <label class="title">{{ child.title }}</label>
                                <div class="content" v-if="child.component">
                                    <component v-if="shown_groups[c_group]"
                                        :is="components[child.component]" 
                                        :condition="child" 
                                        :market-negotiation="marketNegotiation"
                                        :parser="parseRadioGroup"
                                        :defaults="defaults"
                                        @change="e => setCondition(child, e, c_group)"
                                    ></component>
                                </div>
                                <div class="content" v-else>
                                    <b-form-radio-group v-if="child.value.constructor === Array"
                                                        v-active-request
                                                        v-model="defaults[child.alias]"
                                                        v-bind:options="parseRadioGroup(child.value)"
                                                        stacked
                                                        v-on:change="e => setCondition(child, e, c_group)"
                                                        name="">
                                    </b-form-radio-group>
                                </div>
                            </div>
                        </div>

                        <div class="ibar-condition-panel-content text-left" v-else>
                            <b-form-radio-group v-if="condition.value.constructor === Array"
                                                v-active-request
                                                v-model="defaults[condition.alias]"
                                                v-bind:options="parseRadioGroup(condition.value)"
                                                stacked
                                                v-on:change="e => setCondition(condition, e, c_group)"
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
    import conditionFoKSide from './Conditions/FoKSide';
    
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
            conditionPropose,
            conditionFoKSide
        },
        data() {
            return {
                shown_groups: [],
                show_options: false,
                conditions: this.$root.config('market_conditions'),
                components: {
                    'condition-propose': conditionPropose,
                    'condition-fok-side': conditionFoKSide
                },
                defaults: {}
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
                return this.marketRequest._stage;
            },
            condition_aliases() {
                let getAlias = (list, group) => {
                    return list.reduce((a, v, i) => {
                        if(v.alias) {
                            a[v.alias] = {
                                default: v.default,
                                default_value: v.default_value,
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
            parseRadioGroup(group) {
                return group.filter(x => {
                    return x.hidden !== true && this.conditionDisplayed(x);
                });
            },
            conditionDisplayed(cond){
                if(typeof cond.only !== 'undefined') {
                    if(cond.only == this.negotiation_stage) {
                        return true;
                    }
                    return false;
                }
                return true;
            },
            onToggleClick(condition, group) {
                console.log("onToggleClick", condition, group, this.marketNegotiation);
                if(condition.children) {
                    condition.children.forEach(child => {
                        if(typeof child.value !== 'undefined') {
                            if(child.value.constructor == Boolean) {
                                this.setCondition(child, child.value, group);
                            }
                            if(child.value.constructor == Array) {
                                this.setCondition(child, child.value[0], group);
                            }
                        }
                    });
                } else {
                    if(typeof condition.value !== 'undefined') {
                        if(condition.value.constructor == Boolean) {
                            this.setCondition(condition, condition.value, group);
                        }
                        if(condition.value.constructor == Array) {
                            this.setCondition(condition, condition.value[0], group);
                        }
                    }
                }
                this.defaults = {};
                this.setDefaults(condition);
                console.log("Defaults: ", this.defaults, this.marketNegotiation);
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
            getDeepValue(value) {
                switch(value.constructor) {
                    case Object:
                        return this.getDeepValue(value.value);
                    break;
                    default: 
                        return value;
                }
            },
            setDefaults(condition) {
                if(typeof condition.children !== 'undefined') {
                    condition.children.forEach(x => this.setDefaults(x));
                    return;
                }
                switch(condition.value.constructor) {
                    case Array:
                        if(typeof condition.default_value !== 'undefined') {
                            this.defaults[condition.alias] = condition.value.find(item => {
                                if(this.getDeepValue(item) === condition.default_value) {
                                    console.log(" 1====>",condition.alias, this.getDeepValue(item), condition.default_value);
                                    return true;
                                }
                            }).value;
                            this.marketNegotiation[condition.alias] = condition.default_value;
                        }
                    break;
                    default:
                        if(typeof condition.default_value !== 'undefined') {
                            console.log(" 2====>",condition.alias, condition.value, condition.default_value);
                            this.defaults[condition.alias] = condition.value;
                            this.marketNegotiation[condition.alias] = condition.default_value;
                        }
                }
                console.log("Set Data: ", this.defaults[condition.alias], this.marketNegotiation[condition.alias]);
            },
            setCondition(condition, value, group) {
                console.log("SetCondition: ", condition, value, group);
                this.resetConditions(group, value.ignores);
                if(value === null) {
                    this.marketNegotiation[condition.alias] = value;
                } else {
                    switch(value.constructor) {
                        case Object:
                            if(value.value === null) {
                                this.marketNegotiation[condition.alias] = value.value;
                                if(value.sets) {
                                    value.sets.forEach(v => {
                                        this.marketNegotiation[v.alias] = v.value;
                                    });
                                }
                            } else {
                                if(value.value.constructor == Object) {
                                    this.setCondition(condition, value.value, group);
                                } else {
                                    this.marketNegotiation[condition.alias] = value.value;
                                    if(value.sets) {
                                        value.sets.forEach(v => {
                                            this.marketNegotiation[v.alias] = v.value;
                                        });
                                    }
                                }
                            }
                        break;
                        case Boolean:
                        default:
                            this.marketNegotiation[condition.alias] = value;
                    }
                }
                console.log("Set: ", this.marketNegotiation[condition.alias]);
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