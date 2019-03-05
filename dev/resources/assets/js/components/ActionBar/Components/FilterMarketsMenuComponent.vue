<template>
    <div dusk="filter-markets-menu" class="filter-markets-menu">
        <button v-active-request id="action-filter-market-button" type="button" class="btn mm-transparent-button mr-2">
            <span class="icon icon-add"></span> Markets
        </button>
        <!-- Filter market popover -->
        <b-popover @show="onShow" triggers="click blur" placement="bottomleft" :ref="popover_ref" target="action-filter-market-button">
            <div class="row text-center">
                <div class="col-12">
                    <div v-for="(obj,key) in availableSelectedMarketTypes" class="row mt-1">
                        <div class="col-6 text-center pt-2 pb-2">
                            <h5 class="w-1d00 m-0 popover-over-text">{{ key }}</h5>
                        </div>
                        <div class="col-6">
                            <button v-if="obj.state" 
                                    :data-remove-market="key"
                                    type="button" class="btn mm-generic-trade-button w-100"
                                    @click="filterMarketTypes(key, false)"
                                    v-active-request>
                                Remove
                            </button>
                            <button v-else 
                                    :data-add-market="key"
                                    type="button" class="btn mm-generic-trade-button w-100"
                                    @click="filterMarketTypes(key, true)"
                                    v-active-request>
                                Add
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <p class="m-2">DCAP and DTOP: Only displayed when markets are requested</p>
                </div>
                
                <div class="col-6 offset-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss">OK</button>
                </div>
            </div>
        </b-popover>
        <!-- END Filter market popover -->
    </div>
</template>

<script>
	import Market from '~/lib/Market';
    import UserMarket from '~/lib/UserMarket';
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    export default {
        name: 'FilterMarketsMenu',
    	props:{
          'markets': {
            type: Array
          },
          'popoverRef': {
            type: String
          },
          'closeCallback': {
            type: Function
          },
        },
        data() {
            return {
                availableSelectedMarketTypes: {
                    'INDEX': {
                        state: false,
                        marketType: null,
                    },
                    'SINGLES': {
                        state: false,
                        marketType: null,
                    },
                    'DELTA ONE': {
                        state: false,
                        marketType: null,
                    }
                },
                popover_ref: 'add-market-ref',
            };
        },
        methods: {
            /**
             * This is called just before the popover is shown
             */
            onShow () {
                this.checkSelected();
            },
            /**
             * Creates a boolean list of availableSelectedMarkets from markets prop
             */
            checkSelected() {
                this.$root.market_types.forEach( (market_type) => {
                    switch(market_type.title) {
                        case "Index Option":
                            this.availableSelectedMarketTypes['INDEX'].marketType = market_type;
                        break;
                        case "Delta One(EFPs, Rolls and EFP Switches)":
                            this.availableSelectedMarketTypes['DELTA ONE'].marketType = market_type;
                        break;

                        case "Single Stock Options":
                            this.availableSelectedMarketTypes['SINGLES'].marketType = market_type;
                        break;
                    }
                });
                Object.keys(this.availableSelectedMarketTypes).forEach(key=>{
                    this.availableSelectedMarketTypes[key].state = false;
                });
                
                Object.keys(this.availableSelectedMarketTypes).forEach(key=>{
                    this.$root.config("user_preferences.prefered_market_types").forEach( (prefered_market_type) => {
                        if(this.availableSelectedMarketTypes[key].marketType.id == prefered_market_type) {
                            this.availableSelectedMarketTypes[key].state = true;
                        }
                    });
                });
            },
            /**
             * Filters the user's Market Type preference 
             */
            filterMarketTypes(type_key, is_add) {
                if(is_add) {
                    this.addUserPreferenceMarketType(type_key)
                    .then( (market_type_id) => {
                        let market_type = this.$root.market_types.find(element => {
                            return element.id == market_type_id;
                        });
                        this.addMarket(market_type);
                    });
                } else {
                    this.removeUserPreferenceMarketType(type_key)
                    .then( (market_type_id) => {
                        let market_type = this.$root.market_types.find(element => {
                            return element.id == market_type_id;
                        });
                        this.$root.removeTradeConfirmations(market_type);
                        this.removeMarket(type_key);
                    });
                }
            },
            /**
             * Adds a selected Market to Display Markets
             * 
             * @param {string} $market a string detailing a Market.title to be added
             */
            addMarket(market_type) {
                this.$root.loadTradeConfirmations(market_type);
                this.$root.loadMarkets(market_type)
                .then(markets => {
                    let promises = [];
                    markets.forEach(market => {
                        promises.push(
                            this.$root.loadMarketRequests(market)
                        );
                    });
                    return Promise.all(promises);
                });
                this.checkSelected();
            },
            /**
             * Adds a selected Market Type to the users preferences and saves it to the server
             * 
             * @param {string} $market_type a string detailing a MarketType.title to be added
             */
            addUserPreferenceMarketType(market_type) {
                return axios.patch(axios.defaults.baseUrl + '/user-pref/'+ this.availableSelectedMarketTypes[market_type].marketType.id)
                .then(response => {
                    if(response.status == 200) {
                        this.$root.configs["user_preferences"].prefered_market_types.push(response.data.data);
                        return this.$root.configs["user_preferences"].prefered_market_types[this.$root.configs["user_preferences"].prefered_market_types.length-1];
                    } else {
                        return null;
                        //console.error(err);
                    }
                }, err => {
                    //console.error(err);
                });
            },
            /**
             * Removes a selected Market from Display Markets
             * 
             * @param {string} $market_type_key a string detailing a Type to be removed
             */
            removeMarket(market_type_key) {
                let list = this.markets.filter( (market) => {
                    return market.market_type_id == this.availableSelectedMarketTypes[market_type_key].marketType.id;
                });
                list.forEach(market => {
                    this.markets.splice(this.markets.indexOf(market), 1);
                });
                this.checkSelected();
            },
            /**
             * Removes a selected Market Type from the users preferences and saves it to the server
             * 
             * @param {string} $market_type a string detailing a MarketType.title to be removed
             */
            removeUserPreferenceMarketType(market_type) {
                // write delete end to apply new pref
                return axios.delete(axios.defaults.baseUrl + '/user-pref/'+ this.availableSelectedMarketTypes[market_type].marketType.id)
                .then(response => {
                    if(response.status == 200) {
                        let index = this.$root.configs["user_preferences"].prefered_market_types.findIndex(function(element) {
                            return element == response.data.data;
                        });
                        if(index !== -1) {
                            return this.$root.configs["user_preferences"].prefered_market_types.splice(index , 1);
                        }
                        return response.data.data;
                    } else {
                        //console.error(err);
                    }
                }, err => {
                    //console.error(err);
                });
            },
            /**
             * Closes popover
             */
            onDismiss() {
                this.$refs[this.popover_ref].$emit('close');
            },
        },
        mounted() {

        }
    }
</script>