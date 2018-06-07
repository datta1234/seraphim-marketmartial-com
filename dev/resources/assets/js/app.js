
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./components/data-methods');

window.Vue = require('vue');
window.moment = require('moment');

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);
import 'bootstrap-vue/dist/bootstrap-vue.css'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const Market = require('./lib/Market');
const UserMarketRequest = require('./lib/UserMarketRequest');
const UserMarket = require('./lib/UserMarket');
const MarketNegotiation = require('./lib/MarketNegotiation');

Vue.component('user-header', require('./components/UserHeaderComponent.vue'));

// Market Tab Components
Vue.component('market-group', require('./components/MarketGroupComponent.vue'));
Vue.component('market-tab', require('./components/MarketTabComponent.vue'));

// Interaction Bar Component + children
    Vue.component('ibar-negotiation-bar', require('./components/InteractionBar/NegotiationBar.vue'));
    Vue.component('ibar-user-market-title', require('./components/InteractionBar/Components/UserMarketTitle.vue'));
    Vue.component('ibar-negotiation-history', require('./components/InteractionBar/Components/NegotiationHistory.vue'));
    Vue.component('ibar-market-negotiation', require('./components/InteractionBar/MarketComponents/MarketNegotiation.vue'));
    Vue.component('ibar-apply-conditions', require('./components/InteractionBar/MarketComponents/ApplyConditionsComponent.vue'));
Vue.component('interaction-bar', require('./components/InteractionBarComponent.vue'));

// Action Bar Component + children
Vue.component('action-bar', require('./components/ActionBarComponent.vue'));
Vue.component('filter-markets-menu', require('./components/ActionBar/Components/FilterMarketsMenuComponent.vue'));
Vue.component('Important-markets-menu', require('./components/ActionBar/Components/ImportantMenuComponent.vue'));
Vue.component('Alerts-markets-menu', require('./components/ActionBar/Components/AlertsMenuComponent.vue'));
Vue.component('Confirmations-markets-menu', require('./components/ActionBar/Components/ConfirmationsMenuComponent.vue'));

Vue.component('user-header', require('./components/UserHeaderComponent.vue'));
Vue.component('chat-bar', require('./components/ChatBarComponent.vue'));

/**
 * Takes in a value and splits the value by a splitter in a desired frequency
 *
 * @param {string|number} val - the desired value to split
 * @param {string} splitter - the splitter to split the value by
 * @param {number} frequency - the frequency in which to apply the split to the value
 *
 * @return {string} the newly splitted value
 */
let splitValHelper= function (val, splitter, frequency) {
    let tempVal = ('' + val);
    let floatVal = '';
    //Check if our passed value is a float
    if( ("" + val).indexOf('.') !== -1 ) 
    {
        floatVal = tempVal.slice(tempVal.indexOf('.'));
        tempVal = tempVal.slice(0,tempVal.indexOf('.'));
    }
    //Creates an array of chars reverses and itterates through it
    return tempVal.split('').reverse().reduce(function(x,y) {
        //adds a space on the spesified frequency position
        if(x[x.length-1].length == frequency)
        {
           x.push("");
        }
        x[x.length-1] = y+x[x.length-1];
        return x;
    //Concats the array to a string back in the correct order
    }, [""]).reverse().join(splitter) + floatVal;
};

Vue.mixin({
    methods: {
        /**
         * Takes in a value and formats it according to it's size to a currency format
         *
         * @param {string|number} val - the desired value to be formatted
         *
         * @return {string} the formated currency value
         */
        formatRandQty(val) {
            let sbl = "R";
            let calcVal = ( typeof val === 'number' ? val : parseInt(val) );
            //currently they want the format the same for all values
            switch(Math.ceil( ('' + Math.trunc(val)).length / 3)) {
                case 3: // 1 000 000 < x
                    //return sbl+(calcVal/1000000)+"m";
                break;
                case 2: // 1000 < x < 1 000 000
                    //return sbl + splitValHelper( calcVal, ' ', 3);
                case 1: // 100 < x < 1000
                case 0: // x < 100
                default:
                    return sbl + splitValHelper( calcVal, ' ', 3);
            }
        },
    }
});

let sampleUserNegotitaion = new MarketNegotiation({ 
    bid: 30, 
    bid_qty: 50000000, 
    offer: 25, 
    offer_qty: 50000000 
});
let sampleUserMarket = new UserMarket({
    market_negotiations: [
        sampleUserNegotitaion
    ]
});
let marketRequestSample = new UserMarketRequest({
    id: "7",
    attributes: {
        expiration_date: moment("2018-03-18 00:00:00"),
        strike: "10 000",
        state: 'sent',
        bid_state: '',
        offer_state: '',
    },
    user_markets: [sampleUserMarket],
    chosen_user_market: sampleUserMarket
});
let marketRequestSample2 = new UserMarketRequest({
    id: "6",
    attributes: {
        expiration_date: moment("2018-03-20 00:00:00"),
        strike: "12 000",
        state: '',
        bid_state: '',
        offer_state: '',
    },
    user_markets: [
        new UserMarket({
            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
        }),
        new UserMarket({
            current_market_negotiation: new MarketNegotiation({ bid: 25, bid_qty: 50000000, offer: 24, offer_qty: 50000000 })
        }),
        new UserMarket({
            current_market_negotiation: new MarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
        })
    ],
    chosen_user_market: new UserMarket({
        current_market_negotiation: new MarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
    })
});

const app = new Vue({
    el: '#trade_app',
    watch: {
        'display_markets': function(nv, ov) {
            this.reorderDisplayMarkets(nv);
        },
    },
    methods: {
        /**
         * Basic bubble sort that sorts Display Markets according to a set Market Order
         *
         * @param {Array} display_markets_arr - The display market array that need to be sorted
         *
         * @return void
         */
        reorderDisplayMarkets(display_markets_arr) {
            for(let i = 0; i < display_markets_arr.length - 1; i++) {
                for(let j = 0; j < display_markets_arr.length - i - 1; j++) {
                    if( this.market_order.indexOf(display_markets_arr[j].title) > this.market_order.indexOf(display_markets_arr[j+1].title) ) {
                        let temp = display_markets_arr[j];
                        display_markets_arr[j] = display_markets_arr[j+1];
                        display_markets_arr[j+1] = temp;
                    }
                }
            }
        }
    },
    data: {
        // default data
        market_order:['TOP 40','DTOP','DCAP','SINGLES','DELTA ONE'],
        no_cares: [],
        display_markets: [
            new Market({
                title: "TOP 40",
                market_requests: [
                    marketRequestSample,
                    marketRequestSample2,
                    new UserMarketRequest({
                        id: "1",
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                    new UserMarketRequest({
                        attributes: {
                            expiration_date: moment("2018-03-18 00:00:00"),
                            strike: "11 000",
                            state: '',
                            bid_state: '',
                            offer_state: '',
                        },
                        user_markets: [
                            new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                            })
                        ],
                        chosen_user_market: new UserMarket({
                            current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                        })
                    }),
                ]
            }),
            new Market({
                title: "DTOP",
                market_requests: [
                    new UserMarketRequest({
                        id: "2",
                        attributes: {
                            expiration_date: moment("2018-03-17 00:00:00"),
                            strike: "14 000",
                            state: 'vol-spread',
                            vol_spread: 4,
                            bid_state: '',
                            offer_state: '',
                        }
                    })
                ]
            }),
            new Market({
                title: "SINGLES",
                market_requests: [
                    new UserMarketRequest({
                        id: "3",
                        attributes: {
                            expiration_date: moment("2018-03-17 00:00:00"),
                            strike: "16 000",
                            state: 'confirm',
                            bid_state: '',
                            offer_state: 'action',
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
            })
        ]
    }
});

// Testing Code ( Simulate Stream Updates )

// REQUEST - blue
setTimeout(function(){
    console.log("REQUEST - blue");
    app.display_markets[1].addMarketRequest(
        new UserMarketRequest({
            id: "4",
            attributes: {
                expiration_date: moment("2018-03-18 00:00:00"),
                strike: "10 000",
                state: 'request',
                bid_state: '',
                offer_state: '',
            }
        })
    );
}, 5000);

// REQUEST - grey
setTimeout(function(){
    console.log("REQUEST - grey");
    marketRequestSample.attributes.state = "request-grey";
}, 10000);

// VOL SPREAD
setTimeout(function(){
    console.log("VOL SPREAD");
    marketRequestSample.attributes.vol_spread = 3;
    marketRequestSample.attributes.state = 'vol-spread-alert';
}, 10000);

// VOL SPREAD
setTimeout(function(){
    console.log("VOL SPREAD");
    marketRequestSample2._chosen_user_market.setCurrentNegotiation(new MarketNegotiation({ bid: 32, bid_qty: 50000000, offer: 25, offer_qty: 50000000 }))
    marketRequestSample2.attributes.bid_state = 'action';
    marketRequestSample2.attributes.state = '';
}, 15000);

// RESET
setTimeout(function(){
    console.log("RESET STATE");
    sampleUserMarket.setCurrentNegotiation(sampleUserNegotitaion);
    marketRequestSample.setChosenUserMarket(sampleUserMarket);
}, 20000);
