<template>
    <b-row dusk="ibar-apply-conditions">
        <b-col>
            <b-row>
                <b-col>
                    <b-form-checkbox v-model="show_options" value="true" unchecked-value="false"> Apply a condition</b-form-checkbox>
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
                        <div class="ibar-condition-panel-content">
                            
                            <div>
                                <label></label>
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

    export default {
        data() {
            return {
                show_options: false,
                conditions: [],
                categories: [],
            };
        },
        methods: {
            loadConditions() {
                let self = this;
                return axios.get('/trade/market-condition')
                .then(conditionsResponse => {
                    if(conditionsResponse.status == 200) {
                        // set the available market types
                        self.conditions = conditionsResponse.data.conditions || [];
                        self.categories = conditionsResponse.data.categories || [];
                        self.categories.forEach(x => x['opened'] = false);
                    } else {
                        console.error(err);    
                    }
                    return conditionsResponse.data;
                }, err => {
                    console.error(err);
                });
            },
            applyCondition(condition) {
                console.log(condition);
            },
            applyCategory(category) {
                this.categories.forEach(x => x.opened = false); // reset

                category.opened = true;
                console.log(category);
            }
        },
        created() {
            this.loadConditions();
        },
        mounted() {

        }
    }
</script>