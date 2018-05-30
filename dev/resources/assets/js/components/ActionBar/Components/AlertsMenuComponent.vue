<template>
    <div class="Alerts-markets-menu">
        <button id="actionAlertButton" type="button" class="btn mm-alert-button mr-2 p-1">Alerts <strong>{{ count }}</strong></button>
        <!-- Alerts market popover -->
        <b-popover triggers="click blur" placement="bottom" :ref="popover_ref" target="actionAlertButton">
            <div class="row text-center">
                <div v-for="(maket,key) in notificationList" class="col-12">
                    <div v-if="maket.length > 0" v-for="market_request in maket" class="row mt-1">
                        <div class="col-6 text-center">
                            <h6 class="w-100 m-0"> {{ key }} {{ market_request.attributes.strike }} {{ market_request.attributes.expiration_date.format("MMM DD") }}</h6>
                        </div>
                        <div class="col-6">
                            <button 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="loadInteractionBar(market_request)">View
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-6 offset-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss">OK</button>
                </div>
            </div>
        </b-popover>
        <!-- END Alerts market popover -->
    </div>
</template>

<script>
    import { EventBus } from '../../../lib/EventBus.js';  
    export default {
      props:{
          'markets': {
            type: Array
          },
          'count': {
            type: Number
          },
        },
        data() {
            return {
               popover_ref: 'alert-market-ref',
            };
        },
        computed: {
            notificationList: function() {
                let list = this.markets.reduce( function(acc, obj) {
                    acc[obj.title] = obj.market_requests.reduce( function(acc2, obj2) {
                        switch(obj2.attributes.state) {    
                            case "vol-spread-alert":
                            case "alert":
                                return acc2.concat(obj2);
                            break;
                            default:
                                return acc2;
                        }
                    }, []);
                    return acc;
                }, {});
                return list;
            },
        },
        methods: {
            onDismiss() {
                this.$refs[this.popover_ref].$emit('close');
            },
            loadInteractionBar(market_request) {
                console.log("load Bar");
                EventBus.$emit('interactionToggle', true, market_request);
            },
        },
        mounted() {
            
        }
    }
</script>