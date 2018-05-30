<template>
    <div class="Important-markets-menu">
        <button id="actionImportantButton" type="button" class="btn mm-important-button mr-2 p-1" >Important <strong>{{ count }}</strong></button>
        <!-- Important market popover -->
        <b-popover triggers="click blur" placement="bottom" :ref="popover_ref" target="actionImportantButton">
            <div class="row text-center">
                <div v-for="(maket,key) in notificationList" class="col-12">
                    <div v-if="maket.length > 0" v-for="market_requests in maket" class="row mt-2">
                        <div class="col-6 text-center">
                            <h6 class="w-100 m-0"> {{ key }} {{ market_requests.attributes.strike }} {{ market_requests.attributes.expiration_date.format("MMM DD") }}</h6>
                        </div>
                        <div class="col-6">
                            <button 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="addToNoCares(market_requests.id)">No Cares
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <b-form-group>
                        <b-form-checkbox 
                            id="selectBulkNoCares"
                            v-model="status" 
                            value="true">
                            Select All
                        </b-form-checkbox>
                    </b-form-group>
                </div>
                
                <div class="col-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="applyBulkNoCares">OK</button>
                </div>
                <div class="col-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss()">Cancel</button>
                </div>
            </div>
        </b-popover>
        <!-- END Important market popover -->
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
          'no_cares': {
            type: Array
          },
        },
        data() {
            return {
                status: false,
                popover_ref: 'important-market-ref',
            };
        },
        computed: {
            notificationList: function() {
                let list = this.markets.reduce( function(acc, obj) {
                    acc[obj.title] = obj.market_requests.reduce( function(acc2, obj2) {
                        switch(obj2.attributes.state) {    
                            case "vol-spread-alert":
                            case "alert":
                            case "confirm":
                                return acc2;
                            break;
                            default:
                                return acc2.concat(obj2);
                        }
                    }, []);
                    return acc;
                }, {});
                return list;
            },
        },
        methods: {
            onDismiss() {
                this.status = false;
                this.$refs[this.popover_ref].$emit('close');
            },
            addToNoCares(id) {
                if(!this.no_cares.includes(id)) {
                    this.no_cares.push(id);
                }
            },
            applyBulkNoCares() {
                if(this.status) {
                    for (var market in this.notificationList) {
                        if(this.notificationList[market].length > 0) {
                            this.notificationList[market].forEach( (market_request) => {
                                if(!this.no_cares.includes(market_request.id)) {
                                    this.no_cares.push(market_request.id);
                                }
                            });
                        }
                    }
                }
                this.onDismiss();
            },
        },
        mounted() {
            
        }
    }
</script>