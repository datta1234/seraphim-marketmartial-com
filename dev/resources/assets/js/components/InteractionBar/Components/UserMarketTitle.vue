<template>
    <b-row>
        <b-col cols="10">
            <h3>{{ title }}</h3>
        </b-col>
        <b-col cols="2">
            <p class="pull-right">{{ time }}</p>
        </b-col>
    </b-row>
</template>

<script>
    export default {
        props: {
            title: {
                type: String
            },
            time: {
                type: String
            }
        },
        mounted() {

        }
    }
</script>