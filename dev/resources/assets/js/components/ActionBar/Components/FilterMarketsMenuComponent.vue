<template>
    <div dusk="filter-markets-menu" class="filter-markets-menu">
        <button id="action-filter-market-button" type="button" class="btn mm-transparent-button mr-2">
            <span class="icon icon-add"></span> Markets
        </button>
        <!-- Filter market popover -->
        <b-popover @show="onShow" triggers="focus" placement="bottomleft" :ref="popover_ref" target="action-filter-market-button">
            <div class="row text-center">
                <div class="col-12">
                    <div v-for="(obj,key) in availableSelectedMarketTypes" class="row mt-1">
                        <div class="col-6 text-center pt-2 pb-2">
                            <h5 class="w-100 m-0">{{ key }}</h5>
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
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onSaveMarketSetting">OK</button>
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
            onShow () {
                this.init();
                /* This is called just before the popover is shown */
                this.checkSelected();
            },
            /**
             * Saves the user's Market preference to the server
             *
             * @todo implement post reqeust to update user preference
             */
            onSaveMarketSetting(popover_ref) {
                this.onDismiss();
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
                    if(this.availableSelectedMarketTypes[key].marketType !== null) {
                        this.availableSelectedMarketTypes[key].state = true;
                    } else {
                        this.availableSelectedMarketTypes[key].state = false;
                    }
                });
            },
            /**
             * Filters the user's Market Type preference 
             */
            filterMarketTypes(market_type, actionCheck) {
                this.availableSelectedMarketTypes[market_type].marketType.markets.forEach((market) => {
                    if(actionCheck) {
                        this.addMarket(market);
                    } else {
                        this.removeMarket(market);
                    }
                });
            },
            /**
             * Adds a selected Market to Display Markets
             * 
             * @param {string} $market a string detailing a Market.title to be added
             *
             * @todo make a reqeust to update view data from server 
             */
            addMarket(market) {
               //this.markets.push( this.loadMarketData(market) );
               this.checkSelected();
            },
            /**
             * Removes a selected Market from Display Markets
             * 
             * @param {string} $market a string detailing a Market.title to be removed
             *
             * @todo make a push the updated Display Markets list to the server 
             */
            removeMarket(market) {
                let index = this.markets.findIndex(function(element) {
                    return element.id == market.id;
                });
                if(index !== -1) {
                    this.markets.splice(index , 1);
                }
                this.checkSelected();
            },
            /**
             * Closes popover
             */
            onDismiss() {
                this.$refs[this.popover_ref].$emit('close');
            },
            init() {

            }
        },
        mounted() {

        }
    }
</script>