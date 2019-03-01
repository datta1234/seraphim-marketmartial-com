<template>
    <div class="trading-opens col-12 text-center" v-if="show_timer">
            Trading Opens In <span>{{ timer_value }}</span>
    </div>
</template>
<script>
    import moment from 'moment';
    export default {
        props: {
            openTime: {
                type: moment
            }
        },
        data() {
            return {
                show_timer: false,
                timer_value: "00:00:00",
                timer: null
            }
        },
        methods: {
            getRemainingTime() {
                let diff = moment.parseZone(this.openTime).diff(moment());
                // ensure its not shown if its timed out
                if(diff < 0) {
                    return "00:00:00";
                } else {
                    this.show_timer = true;
                    return moment.duration(diff).format("hh:mm:ss");
                }
            },
            startTimer() {
                if(this.timer != null) {
                    this.stopTimer();
                }
                this.timer = setInterval(this.runTimer, 1000);
            },
            runTimer() {
                if(this.openTime != null) {
                    this.timer_value = this.getRemainingTime();
                    if(this.timer_value == "00:00:00") {
                        this.stopTimer();
                    }
                }
            },
            stopTimer() {
                clearInterval(this.timer);
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