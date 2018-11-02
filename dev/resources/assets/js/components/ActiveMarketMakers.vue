<template>
    <span>{{ total }}</span>
</template>
<script>
    import ActiveMakerService from '~/services/ActiveMakerService';

    const component = {
        template: "",
        data() { 
            return { 
                total: 0
            } 
        },
        methods: {
            update(val) {
                this.total = val;
            }
        },
        mounted() {
            ActiveMakerService.interface.attach(this.update);
        }
    };
    export default component;
</script>