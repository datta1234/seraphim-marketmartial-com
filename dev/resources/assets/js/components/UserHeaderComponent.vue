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
                    hours:'',
                    minutes:'',
                    session:'AM',
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
                var date = new Date();
                this.time.hours = date.getHours(); // Hours format 0 - 23
                this.time.minutes = date.getMinutes(); // 0 - 59
                this.time.session = "AM";
                
                //resets the hour when reaching 0 to 12
                if(this.time.hours == 0){
                    this.time.hours = 12; //Changes computed hours to format 0 - 12
                }
                
                //Changes hours from before 12 AM to past 12 PM - keeps to format 0 - 12 
                if(this.time.hours > 12){
                    this.time.hours = this.time.hours - 12;
                    this.time.session = "PM";
                }
                
                //Format time from h:m format to hh:mm format with leading 0
                this.time.hours = (this.time.hours < 10) ? "0" + this.time.hours : this.time.hours;
                this.time.minutes = (this.time.minutes < 10) ? "0" + this.time.minutes : this.time.minutes;         
                this.time.computed_time = this.time.hours + ":" + this.time.minutes + " " + this.time.session;
                
                
            }
        },
        mounted() {
            //Sets interval for running clock
            this.time._interval = setInterval(this.showTime, 1000);
        }
    }
</script>