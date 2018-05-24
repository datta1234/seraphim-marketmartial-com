
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./components/data-methods');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const UserMarket = require('./lib/UserMarket');
const DerivativeMarket = require('./lib/DerivativeMarket');

Vue.component('market-group', require('./components/MarketGroupComponent.vue'));
Vue.component('market-tab', require('./components/MarketTabComponent.vue'));
Vue.component('interaction-bar', require('./components/InteractionBarComponent.vue'));
Vue.component('user-header', require('./components/UserHeaderComponent.vue'));

const app = new Vue({
    el: '#trade_app',
    data: {
        // default data
        display_markets: [
            new DerivativeMarket({
                title: "TOP 40",
                markets: [
                    new UserMarket({
                        date: "Mar 18",
                        strike: "10 000",
                        bid: "10.00",
                        offer: "11.00",
                    }),
                    new UserMarket({
                        date: "Mar 18",
                        strike: "11 000",
                        bid: "13.23",
                        offer: "24.53",
                        state: "request"
                    }),
                    new UserMarket({
                        date: "Mar 20",
                        strike: "22 000",
                        bid: "21.33",
                        offer: "44.22",
                        state: "alert"
                    })
                ]
            }),
            new DerivativeMarket({
                title: "DTOP",
                markets: [
                    new UserMarket({
                        date: "Mar 20",
                        strike: "22 000",
                        bid: "21.33",
                        offer: "44.22",
                        state: "confirm"
                    })
                ]
            }),
            new DerivativeMarket({
                title: "SINGLES",
                markets: [
                    new UserMarket({
                        date: "Mar 20",
                        strike: "22 000",
                        bid: "21.33",
                        offer: "44.22",
                    })
                ]
            })
        ]
    }
});


// Testing Code ( Simulate Stream Updates )

setTimeout(function(){
    console.log("adding");
    app.display_markets[1].markets.push(
        new UserMarket({
            date: "Mar 17",
            strike: "22 000",
            bid: "21.33",
            offer: "44.22",
            state: "request"
        })
    );
}, 5000);

setTimeout(function(){
    console.log("updating");
    app.display_markets[1].markets[0].bid = "99.99";
}, 10000);

setTimeout(function(){
    console.log("updating");
    app.display_markets[1].markets[0].offer = "100.99";
}, 15000);

setTimeout(function(){
    console.log("updating");
    app.display_markets[2].markets[0].state = "confirm";
}, 15000);
