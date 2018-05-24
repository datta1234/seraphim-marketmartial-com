<template>
    <div class="user-header">
        <div class="row sub-nav pt-3 pb-3">
            <div class="col-6">
                <h1 v-if="organisation" class="pt-1">Welcome {{ user_name }} ({{ organisation }})</h1>
                <h1 v-else class="pt-1">Welcome {{ user_name }}</h1>
            </div>
            <div class="col-2">
                <p class="mb-1 pt-3">{{ time.computed_time }}</p>
            </div>
            <div class="col-4">
                <p class="float-right mb-1 pt-3">Rebates: <strong>{{ total_rebate }}</strong></p>
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
                    seconds:'',
                    session:'AM',
                    computed_time:'',
                    _interval: null
                }
            };
        },
        methods: {
            showTime() {
                var date = new Date();
                this.time.hours = date.getHours(); // 0 - 23
                this.time.minutes = date.getMinutes(); // 0 - 59
                this.time.session = "AM";
                
                if(this.time.hours == 0){
                    this.time.hours = 12;
                }
                
                if(this.time.hours > 12){
                    this.time.hours = this.time.hours - 12;
                    this.time.session = "PM";
                }
                
                this.time.hours = (this.time.hours < 10) ? "0" + this.time.hours : this.time.hours;
                this.time.minutes = (this.time.minutes < 10) ? "0" + this.time.minutes : this.time.minutes;         
                this.time.computed_time = this.time.hours + ":" + this.time.minutes + " " + this.time.session;
                
                
            }
        },
        mounted() {
            this.time._interval = setInterval(this.showTime, 1000);
        }
    }
</script>