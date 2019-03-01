<template>
    <div class="trade-closing col-12 text-center" v-if="show_timer">
            {{ timer_value }}
    </div>
</template>
<script>
    export default {
        props: [
            'closingTime'
        ],
        data() {
            return {
                show_timer: false,
                timer_value: "00:00:00",
                timer: null
            }
        },
        methods: {
            getRemainingTime() {
                let diff = moment.parseZone(this.closingTime).diff(moment());
                // ensure its not shown if its timed out
                if(diff < 0) {
                    return "00:00:00";
                } else {

                    // Get the 10 min till closing time
                    let ten_min_diff = moment.duration(diff).subtract(10, 'minutes');
                    // If 10 min till closing is let than 0 then we are in auction
                    if( ten_min_diff < 0 ) {
                        this.show_timer = true;
                        return 'IN AUCTION ' + moment.duration(diff).format("hh:mm:ss");
                    }

                    // Only show timer when we are 20 mins from closing time and default to auction starts
                    if(moment.duration(diff).subtract(20, 'minutes') < 0) {
                        this.show_timer = true;
                    }
                    return 'AUCTION STARTS IN ' + moment.duration(ten_min_diff).format("hh:mm:ss");
                }
            },
            startTimer() {
                if(this.timer != null) {
                    this.stopTimer();
                }
                this.timer = setInterval(this.runTimer, 1000);
            },
            runTimer() {
                if(this.closingTime != null) {
                    this.timer_value = this.getRemainingTime();
                    if(this.timer_value == "00:00:00") {
                        this.stopTimer();
                    }
                }
            },
            stopTimer() {
                clearInterval(this.timer);
                this.show_timer = false;
                this.timer = null;
            }
        },
        mounted() {
            this.startTimer();
            this.runTimer(); // force initial setting
        },
        beforeDestroy() {
            this.stopTimer();
        }
    }
</script>