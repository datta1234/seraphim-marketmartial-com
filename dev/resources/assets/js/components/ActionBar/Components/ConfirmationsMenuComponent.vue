<template>
    <div class="Confirmations-markets-menu">
        <button id="actionConfirmationsButton" type="button" class="btn mm-confirmation-button mr-2 p-1">Confirmations <strong>{{ count }}</strong></button>
        <!-- Confirmations market popover -->
        <b-popover triggers="click blur" placement="bottom" :ref="popover_ref" target="actionConfirmationsButton">
            <div class="row text-center">
                <div v-for="(maket,key) in notificationList" class="col-12">
                    <div v-if="maket.length > 0" v-for="market_requests in maket" class="row mt-1">
                        <div class="col-6 text-center">
                            <h6 class="w-100 m-0"> {{ key }} {{ market_requests.attributes.strike }} {{ market_requests.attributes.expiration_date.format("MMM DD") }}</h6>
                        </div>
                        <div class="col-6">
                            <button 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="removeMarket(key)">View
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-6 offset-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss">OK</button>
                </div>
            </div>
        </b-popover>
        <!-- END Confirmations market popover -->
    </div>
</template>

<script>
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
                popover_ref: 'confirmation-market-ref', 
            };
        },
        computed: {
            notificationList: function() {
                let list = this.markets.reduce( function(acc, obj) {
                    acc[obj.title] = obj.market_requests.reduce( function(acc2, obj2) {
                        switch(obj2.attributes.state) {    
                            case "confirm":
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
        },
        mounted() {
            
        }
    }
</script>