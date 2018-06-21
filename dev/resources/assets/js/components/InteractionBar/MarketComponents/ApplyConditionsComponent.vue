<template>
    <b-row dusk="ibar-apply-conditions">
        <b-col>
            <b-row>
                <b-col>
                    <b-form-checkbox v-model="show_options" value="true" unchecked-value="false"> Apply a condition</b-form-checkbox>
                </b-col>
            </b-row>
            <b-row v-if="show_options" class="text-center">
                <b-col cols="12">
                    Select a Condition
                </b-col>
            </b-row>
        </b-col>
    </b-row>
</template>
<script>
    export default {
        data() {
            return {
                show_options: false,
            };
        },
        mounted() {

        }
    }
</script>