<template>
    <div class="filter-markets-menu">
        <button id="actionfilterMarketButton" type="button" class="btn mm-transparent-button mr-2">
            <span class="icon icon-add"></span> Markets
        </button>
        <!-- Filter market popover -->
        <b-popover triggers="click blur" placement="bottomleft" :ref="popover_ref" target="actionfilterMarketButton">
            <div class="row text-center">
                <div class="col-12">
                    <div v-for="(market,key) in availableSelectedMarkets" class="row mt-1">
                        <div class="col-6 text-center pt-2 pb-2">
                            <h5 class="w-100 m-0">{{ key }}</h5>
                        </div>
                        <div class="col-6">
                            <button v-if="market" 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="removeMarket(key)">Remove
                            </button>
                            <button v-else 
                                type="button" class="btn mm-generic-trade-button w-100"
                                @click="addMarket(key)">Add
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <p class="m-2">DCap and Dtop: Only displaed when markets are requested</p>
                </div>
                
                <div class="col-12 mt-2">
                    <b-form-group>
                        <b-form-checkbox value="saveMarketDefault">Set current as my default</b-form-checkbox>
                    </b-form-group>
                </div>
                
                <div class="col-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onSaveMarketSetting">OK</button>
                </div>
                <div class="col-6 mt-1">
                    <button type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss">Cancel</button>
                </div>
            </div>
        </b-popover>
        <!-- END Filter market popover -->
    </div>
</template>

<script>
	const Market = require('../../../lib/Market');
    const UserMarket = require('../../../lib/UserMarket');
    const UserMarketRequest = require('../../../lib/UserMarketRequest');
    const MarketNegotiation = require('../../../lib/MarketNegotiation');
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
               availableSelectedMarkets: {
                    'TOP 40': false,
                    'DTOP': false,
                    'DCAP': false,
                    'SINGLES': false,
                    'Roll': false,
                },
                randomID: "5", //@TODO REMOVE WHEN ID's ARE ADDED
                popover_ref: 'add-market-ref',
            };
        },
        methods: {
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
                Object.keys(this.availableSelectedMarkets).forEach(key=>{
                    this.availableSelectedMarkets[key] = false;
                });
                this.markets.forEach((market) => {
                    this.availableSelectedMarkets[market.title] = true;
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
                    console.log(element.title);
                    return element.title == market;
                });
                this.markets.splice(index , 1);
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
                                    current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                                }),
                                new UserMarket({
                                    current_market_negotiation: new MarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
                                })
                            ],
                            chosen_user_market: new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
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
            this.checkSelected();
        }
    }
</script>