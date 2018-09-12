<template>
    <b-row dusk="ibar-apply-conditions">
        <b-col>
            <b-row>
                <b-col>
                    <b-form-checkbox v-model="show_options" dusk="ibar-apply-a-condition" name="apply-a-condition" :value="true" :unchecked-value="false"> Apply a condition</b-form-checkbox>
                </b-col>
            </b-row>
            <b-row v-if="show_options" class="text-center" role="tablist">
            
                <b-col v-for="(condition, key) in conditions" cols="12" v-if="condition.hidden !== true">
                    <b-btn @click="e => typeof condition.value === 'boolean' && setCondition(condition.alias, condition.value)"
                            v-b-toggle="'condition_'+key" 
                            variant="default" 
                            size="sm" 
                            class="w-75 mt-2 ibar-condition" 
                            role="tab">
                        {{ condition.title }}
                    </b-btn>
                    <b-collapse :id="'condition_'+key" 
                                class="w-75 ibar-condition-panel" 
                                accordion="conditions" 
                                role="tabpanel">

                        <div class="ibar-condition-panel-content text-left" v-if="condition.children && condition.children.length > 0">
                            <div v-for="child in condition.children">
                                <label class="title">{{ child.title }}</label>
                                <div class="content">
                                    <b-form-radio-group v-if="child.value.constructor === Array"
                                                        v-bind:options="child.value"
                                                        stacked
                                                        v-on:change="e => setCondition(child.alias, e)"
                                                        name="">
                                    </b-form-radio-group>
                                </div>
                            </div>
                        </div>

                        <div class="ibar-condition-panel-content text-left" v-else>
                            <b-form-radio-group v-if="condition.value.constructor === Array"
                                                v-bind:options="condition.value"
                                                stacked
                                                v-on:change="e => setCondition(condition.alias, e)"
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
    import axios from 'axios';

    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import UserMarketNegotiationCondition from '~/lib/UserMarketNegotiationCondition';

    /*
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
        props: {
            marketNegotiation: {
                type: UserMarketNegotiation,
                default: null
            },
            removableConditions: {
                type: Array,
                default: []
            }
        },
        data() {
            return {
                show_options: false,
                conditions: this.$root.config('market_conditions')
            };
        },
        computed: {
            condition_aliases() {
                let getAlias = (list) => {
                    return list.reduce((a, v) => {
                        if(v.alias) {
                            a[v.alias] = v.default;
                        }
                        if(v.children) {
                            Object.assign(a, getAlias(v.children));
                        }
                        return a;
                    }, {});
                };
                return getAlias(this.conditions);
            }
        },
        methods: {
            setCondition(alias, value) {
                console.log("Condition Set: ", alias, value);
                this.resetConditions();
            },
            resetConditions() {
                this.condition_aliases.forEach((d, k) => {
                    console.log(d,k);
                    this.marketNegotiation[k] = d;
                });
            }
        },
        created() {

        },
        mounted() {
            console.log(this.condition_aliases);
        }
    }
</script>