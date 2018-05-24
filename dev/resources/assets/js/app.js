
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
Vue.component('interaction-bar', require('./components/InteractionBarComponent.vue'));
    Vue.component('ibar-negotiation-bar', require('./components/InteractionBar/NegotiationBar.vue'));
    Vue.component('ibar-user-market-title', require('./components/InteractionBar/Components/UserMarketTitle.vue'));
    Vue.component('ibar-negotiation-history', require('./components/InteractionBar/Components/NegotiationHistory.vue'));
    Vue.component('ibar-market-negotiation', require('./components/InteractionBar/MarketComponents/MarketNegotiation.vue'));

Vue.component('user-header', require('./components/UserHeaderComponent.vue'));
Vue.component('action-bar', require('./components/ActionBarComponent.vue'));
Vue.component('chat-bar', require('./components/ChatBarComponent.vue'));

Vue.mixin({
    methods: {
        formatRandQty(val) {
            let sbl = "R";
            let calcVal = ( typeof val === 'number' ? val : parseInt(val) );
            switch(Math.ceil((""+calcVal).length / 3)) {
                case 3: // 1 000 000 < x
                    return sbl+(calcVal/1000000)+"m";
                break;
                case 2: // 1000 < x < 1 000 000
                    return sbl+(calcVal/1000)+"k";
                break;
                case 1: // 100 < x < 1000
                case 0: // x < 100
                default:
                    return sbl+calcVal;
            }
        }
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
    data: {
        // default data
        display_markets: [
            new Market({
                title: "TOP 40",
                market_requests: [
                    marketRequestSample,
                    marketRequestSample2,
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
                    })
                ]
            }),
            new Market({
                title: "DTOP",
                market_requests: [
                    new UserMarketRequest({
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
    app.display_markets[1].market_requests.push(
        new UserMarketRequest({
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
