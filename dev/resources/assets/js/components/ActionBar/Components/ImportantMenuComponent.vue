<template>
    <div dusk="important-markets-menu" class="important-markets-menu">
        <button id="actionImportantButton" type="button" class="btn mm-important-button mr-2 p-1" >Important <strong>{{ count }}</strong></button>
        <div id="importantPopover"></div>
        <!-- Important market popover -->
        <b-popover container="importantPopover" triggers="focus" placement="bottom" :ref="popover_ref" target="actionImportantButton">
            <div class="row text-center">
                <div v-for="(maket,key) in notificationList" class="col-12">
                    <div v-if="maket.length > 0" v-for="market_requests in maket" class="row mt-2">
                        <div class="col-6 text-center">
                            <h6 class="w-100 m-0"> {{ key }} {{ market_requests.attributes.strike }} {{ market_requests.attributes.expiration_date.format("MMM DD") }}</h6>
                        </div>
                        <div class="col-6">
                            <button 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="addToNoCares(key, market_requests.id)">No Cares
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
            /**
             * Compiles a notification list for Important market reqeusts with Market as key
             *      and a market requests array as value
             *
             * @return {Object} in format {/lib/Market.title: /lib/UserMarketRequest [] }
             */
            notificationList: function() {
                //Iterates through an array of Markets and compiles an object with Market.title as key
                return this.markets.reduce( function(acc, obj) {
                    //Iterates through an array of UserMarketRequests and compiles a new array of Important UserMarketRequests 
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
            },
        },
        methods: {
            /**
             * Closes popover
             */
            onDismiss() {
                this.status = false;
                this.$refs[this.popover_ref].$emit('close');
            },
            /**
             * Adds a single Important UserMarketRequest to no cares list and removes it from Markets array
             *
             * @param {string} $market a string detailing the related Market.title
             * @param {string} $id a string id detailing the UserMarketRequests to be removed
             *
             * @todo Change $market to be the Market.id not Market.title
             */
            addToNoCares(market, id) {
                if(!this.no_cares.includes(id)) {
                    this.no_cares.push(id);
                    this.removeMarketRequest(market, id);
                }
            },
            /**
             * Adds all Important UserMarketRequest to no cares list and removes them from Markets array and
             *      closes the popover
             *
             * @todo Change market to be the Market.id not Market.title
             */
            applyBulkNoCares() {
                if(this.status) {
                    for (var market in this.notificationList) {
                        if(this.notificationList[market].length > 0) {
                            this.notificationList[market].forEach( (market_request) => {
                                if(!this.no_cares.includes(market_request.id)) {
                                    this.no_cares.push(market_request.id);
                                    this.removeMarketRequest(market, market_request.id);
                                }
                            });
                        }
                    }
                }
                this.onDismiss();
            },
            /**
             * Removes a single Important UserMarketRequest by id from the Markets array
             *
             * @param {string} $market a string detailing the related Market.title
             * @param {string} $market_request_id a string id detailing the UserMarketRequests to be removed
             *
             * @todo Change $market to be the Market.id not Market.title
             */
            removeMarketRequest(market, market_request_id) {
                let market_index = this.markets.findIndex( (element) => {
                    return element.title == market;
                });
                let market_request_index = this.markets[market_index].market_requests.findIndex( (element) => {
                    return element.id == market_request_id;
                });
                this.markets[market_index].market_requests.splice(market_request_index, 1);
            },
        },
        mounted() {

        }
    }
</script>