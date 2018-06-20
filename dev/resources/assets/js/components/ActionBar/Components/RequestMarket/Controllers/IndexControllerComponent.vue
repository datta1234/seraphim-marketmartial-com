<template>
    <div dusk="index-controller" class="index-controller">
        I CAN HAS CONTROLLER
    </div>
</template>

<script>
    export default {
        name: 'IndexController',
        components: {
            
        },
        props:{
            'callback': {
                type: Function
            },
            'modal_data': {
                type: Object
            }
        },
        data() {
            return {
            };
        },
        methods: {
            /**
             * Returns to previous modal step 
             */
            previousStep() {
                this.modal_data.step--;
            },
            /**
             * Goes to next modal step 
             */
            nextStep(data) {
                this.modal_data.step++;
            },
        },
        mounted() {
        }
    }
</script>