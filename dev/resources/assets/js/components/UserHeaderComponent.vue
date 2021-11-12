<template>
    <div dusk="user-header" class="user-header">
        <div class="row sub-nav pt-3">
            <div class="col-6 user-details">
                <h1 v-if="organisation" class="pt-1">Welcome {{ user_name }} ({{ organisation }})</h1>
                <h1 v-else class="pt-1">Welcome {{ user_name }}</h1>
            </div>
            <div class="col-2 current-time">
                <p class="pt-1">{{ time.display_time }}</p>
            </div>
            <!-- <div class="col-4 total-rebate">
                <p class="float-right pt-1">Rebates: <strong>{{ formatRandQty(displayRebate) }}</strong></p>
            </div> -->
        </div>
    </div>
</template>

<script>
    //lib imports
    import { EventBus } from '~/lib/EventBus.js';
    export default {
        props: [
            'user_name',
            'organisation',
            'total_rebate',
            'server_time'
        ],
        data() {
            return {
                time:{
                    display_time: '',
                    location:'SA',
                    computed_time:null,
                    _interval: null
                },
                displayRebate: null
            };
        },
        methods: {
            /**
             * Creates a running clock from the current date and time.
             */
            timeStep() {
                //Getting current time and setting our time object.
                this.time.computed_time = this.time.computed_time.add(1,'seconds');
                this.time.display_time = this.time.computed_time.format('HH:mm') + ' ' + this.time.location;
            },
            /**
             * Listens for a pageLoaded event firing
             *
             * @event /lib/EventBus#pageLoaded
             */
            rebateListener() {
                EventBus.$on("rebateUpdate",(data)=>{
                    this.displayRebate = data.total;
                });
            },
        },
        created() {
            this.time.computed_time = moment.parseZone(this.server_time);
        },
        mounted() {
            //Sets interval for running clock
            this.time._interval = setInterval(this.timeStep, 1000);
            this.displayRebate = this.total_rebate;
            this.rebateListener();

        }
    }
</script>