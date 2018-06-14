<template>
    <div dusk="filter-markets-menu" class="filter-markets-menu">
        <button id="actionfilterMarketButton" type="button" class="btn mm-transparent-button mr-2">
            <span class="icon icon-add"></span> Markets
        </button>
        <!-- Filter market popover -->
        <b-popover @show="onShow" triggers="focus" placement="bottomleft" :ref="popover_ref" target="actionfilterMarketButton">
            <div class="row text-center">
                <div class="col-12">
                    <div v-for="(obj,key) in availableSelectedMarketTypes" class="row mt-1">
                        <div class="col-6 text-center pt-2 pb-2">
                            <h5 class="w-100 m-0">{{ key }}</h5>
                        </div>
                        <div class="col-6">
                            <button v-if="obj.state" 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="filterMarketTypes(key, false)">Remove
                            </button>
                            <button v-else 
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
                        markets: ['TOP 40','DTOP','DCAP'],
                    },
                    'SINGLES': {
                        state: false,
                        markets: ['SINGLES'],
                    },
                    'DELTA ONE': {
                        state: false,
                        markets: ['DELTA ONE'],
                    }
                },
                randomID: "5", //@TODO REMOVE WHEN ID's ARE ADDED
                popover_ref: 'add-market-ref',
            };
        },
        methods: {
            onShow () {
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
             * Creates a bolean list of availableSelectedMarkets from markets prop
             */
            checkSelected() {
                Object.keys(this.availableSelectedMarketTypes).forEach(key=>{
                    this.availableSelectedMarketTypes[key].state = false;
                });
                this.markets.forEach((market) => {
                    Object.keys(this.availableSelectedMarketTypes).forEach(key=>{
                        if( this.availableSelectedMarketTypes[key].markets.includes(market.title) )
                        this.availableSelectedMarketTypes[key].state = true;
                    });
                });
            },
            /**
             * Filters the user's Market Type preference 
             */
            filterMarketTypes(market_type, actionCheck) {
                this.availableSelectedMarketTypes[market_type].markets.forEach((market) => {
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
               this.markets.push( this.loadMarketData(market) );
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
                    return element.title == market;
                });
                if(index !== -1) {
                    this.markets.splice(index , 1);
                }
                this.checkSelected();
            },
            /**
             * Creates dummy market reqeusts for Newly added Market
             *
             * @todo remove once data is being pulled from server 
             */
            loadMarketData(market) {
                this.randomID += this.randomID + "1"
                return new Market({
                    title: market,
                    market_requests: [
                        new UserMarketRequest({
                            id: this.randomID,
                            attributes: {
                                expiration_date: moment("2018-03-17 00:00:00"),
                                strike: "17 000",
                                state: 'confirm',
                                bid_state: 'action',
                                offer_state: '',
                            },
                            user_markets: [
                                new UserMarket({
                                    current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                                }),
                                new UserMarket({
                                    current_market_negotiation: new UserMarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
                                })
                            ],
                            chosen_user_market: new UserMarket({
                                current_market_negotiation: new UserMarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
                            })
                        })
                    ]
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