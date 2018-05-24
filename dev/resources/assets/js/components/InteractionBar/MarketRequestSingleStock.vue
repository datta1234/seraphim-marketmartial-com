<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12"></div>
        </div>
    </div>
</template>

<script>
    const Market = reuqire('../../lib/Market');
    export default {
        props: {
            market: {
                type: Market
            }
        },
        data() {
            return {
                opened: false,
                subject: null
            };
        },
        methods: {
            toggleBar(set, subject) {
                this.subject = subject;
                if(typeof set != 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }
            }
        },
        mounted() {
            EventBus.$on('interactionToggle', this.toggleBar)
        }
    }
</script>