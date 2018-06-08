<template>
    <div class="user-header">
        <div class="row sub-nav pt-3">
            <div class="col-6">
                <h1 v-if="organisation" class="pt-1">Welcome {{ user_name }} ({{ organisation }})</h1>
                <h1 v-else class="pt-1">Welcome {{ user_name }}</h1>
            </div>
            <div class="col-2">
                <p class="pt-1">{{ time.computed_time }}</p>
            </div>
            <div class="col-4">
                <p class="float-right pt-1">Rebates: <strong>{{ formatRandQty(total_rebate) }}</strong></p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'user_name',
            'organisation',
            'total_rebate'
        ],
        data() {
            return {
                time:{
                    location:'SA',
                    computed_time:'',
                    _interval: null
                }
            };
        },
        methods: {
            /**
             * Creates a running clock from the current date and time.
             *
             * @todo Change clock time to be sent an initialsed from the backend
             */
            showTime() {
                //Getting current time and setting our time object.
                this.time.computed_time = moment().format('HH:mm') + ' ' + this.time.location;
            }
        },
        mounted() {
            //Sets interval for running clock
            this.time._interval = setInterval(this.showTime, 1000);
        }
    }
</script>