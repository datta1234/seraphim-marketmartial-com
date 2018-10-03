window.moment = require('moment');

import Market from './lib/Market';
import UserMarketRequest from './lib/UserMarketRequest';
import UserMarket from './lib/UserMarket';
import UserMarketNegotiation from './lib/UserMarketNegotiation';

let sampleUserNegotitaion = new UserMarketNegotiation({ 
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
            current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
        }),
        new UserMarket({
            current_market_negotiation: new UserMarketNegotiation({ bid: 25, bid_qty: 50000000, offer: 24, offer_qty: 50000000 })
        }),
        new UserMarket({
            current_market_negotiation: new UserMarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
        })
    ],
    chosen_user_market: new UserMarket({
        current_market_negotiation: new UserMarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
    })
});

let markets = [
    new Market({
        id: "1",
        order: 1,
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
                        current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                    })
                ],
                chosen_user_market: new UserMarket({
                    current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                })
            }),
            new UserMarketRequest({
                id: "5",
                attributes: {
                    expiration_date: moment("2018-03-18 00:00:00"),
                    strike: "11 000",
                    state: '',
                    bid_state: '',
                    offer_state: '',
                },
                user_markets: [
                    new UserMarket({
                        current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                    })
                ],
                chosen_user_market: new UserMarket({
                    current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                })
            }),
            new UserMarketRequest({
                id: "6",
                attributes: {
                    expiration_date: moment("2018-03-18 00:00:00"),
                    strike: "11 000",
                    state: '',
                    bid_state: '',
                    offer_state: '',
                },
                user_markets: [
                    new UserMarket({
                        current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                    })
                ],
                chosen_user_market: new UserMarket({
                    current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                })
            }),
            new UserMarketRequest({
                id: "7",
                attributes: {
                    expiration_date: moment("2018-03-18 00:00:00"),
                    strike: "11 000",
                    state: '',
                    bid_state: '',
                    offer_state: '',
                },
                user_markets: [
                    new UserMarket({
                        current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                    })
                ],
                chosen_user_market: new UserMarket({
                    current_market_negotiation: new UserMarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                })
            }),
        ]
    }),
    new Market({
        id: "2",
        order: 2,
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
        id: "4",
        order: 4,
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
    })
];

let app = null;
export default {
    setApp: function(ctx) {
        app = ctx;
    },
    init: function() {
        app.display_markets = markets;
    }
};

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
    marketRequestSample2.chosen_user_market.setCurrentNegotiation(new UserMarketNegotiation({ bid: 32, bid_qty: 50000000, offer: 25, offer_qty: 50000000 }))
    marketRequestSample2.attributes.bid_state = 'action';
    marketRequestSample2.attributes.state = '';
}, 15000);

// RESET
setTimeout(function(){
    console.log("RESET STATE");
    sampleUserMarket.setCurrentNegotiation(sampleUserNegotitaion);
    marketRequestSample.setChosenUserMarket(sampleUserMarket);
}, 20000);