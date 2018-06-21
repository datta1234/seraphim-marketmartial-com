<template>
    <b-row dusk="ibar-apply-conditions">
        <b-col>
            <b-row>
                <b-col>
                    <b-form-checkbox v-model="show_options" :value="true" :unchecked-value="false"> Apply a condition</b-form-checkbox>
                </b-col>
            </b-row>
            <b-row v-if="show_options" class="text-center" role="tablist">
                <b-col cols="12">
                    Select a Condition
                </b-col>
                <b-col cols="12" v-for="condition in conditions">
                    <b-btn @click="applyCondition(condition)" 
                            v-b-toggle="'condition_'+condition.id" 
                            variant="default" 
                            size="sm" 
                            class="w-75 mt-2 ibar-condition" 
                            role="tab">
                        {{ condition.title }}
                    </b-btn>
                    <b-collapse :id="'condition_'+condition.id" 
                                class="w-75 ibar-condition-panel" 
                                accordion="conditions" 
                                role="tabpanel">
                        <!--empty-->
                    </b-collapse>
                </b-col>
                <b-col cols="12" v-for="category in categories">
                    <b-btn @click="applyCategory(category)"
                            v-b-toggle="'category_'+category.id" 
                            variant="default" 
                            size="sm" 
                            class="w-75 mt-2 ibar-condition" 
                            role="tab">
                        {{ category.title }}
                    </b-btn>
                    <b-collapse :id="'category_'+category.id" 
                                class="w-75 ibar-condition-panel" 
                                accordion="conditions" 
                                role="tabpanel">
                        <div class="ibar-condition-panel-content text-left">
                            
                            <div v-for="child in category.children" v-if="category.children.length > 0">
                                <label class="title">{{ child.title }}</label>
                                <div class="content">
                                    <b-form-radio-group v-model="child.selected"
                                                        v-bind:options="getCategoryOptions(child.market_conditions)"
                                                        stacked
                                                        v-on:change="updateCategoryConditions"
                                                        name="">
                                    </b-form-radio-group>
                                </div>
                            </div>
                            <div v-if="category.children.length == 0">
                                <div class="content">
                                    <b-form-radio-group v-model="category.selected"
                                                        v-bind:options="getCategoryOptions(category.market_conditions)"
                                                        stacked
                                                        v-on:change="updateCategoryConditions"
                                                        name="">
                                    </b-form-radio-group>
                                </div>
                            </div>

                        </div>
                    </b-collapse>
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>
<script>
    import axios from 'axios';

    import UserMarketNegotiationCondition from '../../../lib/UserMarketNegotiationCondition';

    export default {
        props: {
            appliedConditions: {
                type: Array,
                default: []
            },
            removableConditions: {
                type: Array,
                default: []
            }
        },
        data() {
            return {
                show_options: false,
                chosen_top_level_category: null,
                conditions: [],
                categories: [],
            };
        },
        watch: {
            'show_options': function(nV) {
                this.appliedConditions.splice(0, this.appliedConditions.length);
                this.removableConditions.splice(0, this.removableConditions.length);
                this.resetCategorySelection(this.categories);
            }
        },
        methods: {
            getCategoryOptions(conditions) {
                let options = [];
                conditions.forEach(x => {
                    options.push({ text: x.title, value: x });
                });
                return options;
            },
            loadConditions() {
                let self = this;
                return axios.get('/trade/market-condition')
                .then(conditionsResponse => {
                    if(conditionsResponse.status == 200) {
                        // set the available market types
                        self.conditions = conditionsResponse.data.conditions.map(x => new UserMarketNegotiationCondition(x)) || [];
                        self.categories = conditionsResponse.data.categories || [];
                        this.resetCategorySelection(this.categories);
                    } else {
                        console.error(err);    
                    }
                    return conditionsResponse.data;
                }, err => {
                    console.error(err);
                });
            },
            resetCategorySelection(cats) {
                cats.forEach(cat => {
                    cat.selected = null;
                    if(cat.children) {
                        this.resetCategorySelection(cat.children);
                    }
                });
            },
            applyCondition(condition) {
                this.appliedConditions.splice(0, this.appliedConditions.length);
                // add condition if not already exists
                if(this.appliedConditions.indexOf(condition) == -1) {
                    this.appliedConditions.push(condition);
                }

                let removable = {
                    title: condition.title
                };
                removable.callback = () => {
                    this.appliedConditions.splice(0, this.appliedConditions.length);
                    this.removableConditions.splice(this.removableConditions.indexOf(removable), 1);
                    this.show_options = false;
                    Vue.nextTick(() => {
                        this.show_options = true;
                    });
                };
                this.removableConditions.splice(0, this.removableConditions.length);
                this.removableConditions.push(removable);
            },
            applyCategory(category) {
                this.chosen_top_level_category = category;
                this.resetCategorySelection(this.categories);
                this.updateCategoryConditions();

                let removable = {
                    title: category.title
                };
                removable.callback = () => {
                    this.chosen_top_level_category = null;
                    this.removableConditions.splice(this.removableConditions.indexOf(removable), 1);
                    this.resetCategorySelection(this.categories);
                    this.updateCategoryConditions();
                    this.show_options = false;
                    Vue.nextTick(() => {
                        this.show_options = true;
                    });
                };
                this.removableConditions.splice(0, this.removableConditions.length);
                this.removableConditions.push(removable);
            },
            updateCategoryConditions(changed) {
                Vue.nextTick(() => {
                    this.appliedConditions.splice(0, this.appliedConditions.length);
                    let recurseSelected = function(category) {
                        let selected = [];
                        if(category.children)
                        category.children.forEach((child) => {
                            selected = selected.concat(recurseSelected(child));
                        });
                        if(category.selected) {
                            selected.push(category.selected);
                        }
                        return selected;
                    };

                    if(this.chosen_top_level_category) {
                        recurseSelected(this.chosen_top_level_category).forEach(newItem => {
                            this.appliedConditions.push(newItem);
                        });
                    }
                });
            }
        },
        created() {
            this.loadConditions();
        },
        mounted() {

        }
    }
</script>