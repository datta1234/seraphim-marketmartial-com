<template>
    <div dusk="all-market-activity" class="all-market-activity" >
        <b-row class="text-center mt-5">
            <b-col>
                <div class="btn-group" role="group">
                    <button type="buttton"
                            v-bind:class="{ active: is_mm_table }"
                            class="btn mm-button selection-button mm-nav-button" 
                            @click="setActive(true)">
                        Market Martial
                    </button>
                    <button type="buttton"
                            v-bind:class="{ active: !is_mm_table }" 
                            class="btn mm-button selection-button mm-nav-button" 
                            @click="setActive(false)">
                        Safex
                    </button>
                </div>

                
            </b-col>
        </b-row>
        <activity-year-tables :hidden="!is_mm_table" :is_my_activity="false" :years="years"></activity-year-tables>
        <safex-table :hidden="is_mm_table"></safex-table>
    </div>
</template>

<script>
    export default {
    	props: {
            'years': {
                type: Array
            },
        },
        data() {
            return {
            	is_mm_table: true,
            };
        },
        methods: {
            /**
             * Sets active component tab
             */
            setActive(mm_active) {
                this.is_mm_table = mm_active;
            },
        },
        mounted() {
        	
        }
    }
</script>