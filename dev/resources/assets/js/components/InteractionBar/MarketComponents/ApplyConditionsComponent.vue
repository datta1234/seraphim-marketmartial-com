<template>
    <b-row dusk="ibar-apply-conditions">
        <b-col>
            <b-form-checkbox v-model="show_options" value="true" unchecked-value="false"> Apply a condition</b-form-checkbox>
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