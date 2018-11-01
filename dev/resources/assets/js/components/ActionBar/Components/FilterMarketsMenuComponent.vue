<template>
    <div dusk="filter-markets-menu" class="filter-markets-menu">
        <button id="action-filter-market-button" type="button" class="btn mm-transparent-button mr-2">
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
                            <button id="" v-if="obj.state" :data-remove-market="key"
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="filterMarketTypes(key, false)">Remove
                            </button>
                            <button v-else :data-add-market="key"
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="filterMarketTypes(key, true)">Add
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <p class="m-2">DCap and Dtop: Only displayed when markets are requested</p>
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
	import Market from '../../../lib/Market';
    import UserMarket from '../../../lib/UserMarket';
    import UserMarketRequest from '../../../lib/UserMarketRequest';
    import UserMarketNegotiation from '../../../lib/UserMarketNegotiation';
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
                        if(this.availableSelectedMarketTypes[key].marketType.id == prefered_market_type.id) {
                            this.availableSelectedMarketTypes[key].state = true;
                        }
                    });
                });
            },
            /**
             * Filters the user's Market Type preference 
             */
            filterMarketTypes(market_type, actionCheck) {
                if(actionCheck) {
                    this.addUserPreferenceMarketType(market_type)
                    .then( (market_type) => {
                        this.addMarket(market_type);
                    });
                } else {
                    this.removeUserPreferenceMarketType(market_type)
                    .then( () => {
                        this.$root.removeTradeConfirmations(market_type);
                        this.availableSelectedMarketTypes[market_type].marketType.markets.forEach((market) => {
                            this.removeMarket(market);
                        });
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
                        console.error(err);
                    }
                }, err => {
                    console.error(err);
                });
            },
            /**
             * Removes a selected Market from Display Markets
             * 
             * @param {string} $market a string detailing a Market.title to be removed
             */
            removeMarket(market) {
                let index = this.markets.findIndex(function(element) {
                    return element.id == market.id;
                });
                if(index !== -1) {
                    this.markets.splice(index , 1);
                }
                // make api call to amend user pref for market types
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
                            return element.id == response.data.data;
                        });
                        if(index !== -1) {
                            return this.$root.configs["user_preferences"].prefered_market_types.splice(index , 1);
                        }
                        return null;
                    } else {
                        console.error(err);
                    }
                }, err => {
                    console.error(err);
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