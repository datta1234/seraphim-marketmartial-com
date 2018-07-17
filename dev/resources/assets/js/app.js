
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./components/data-methods');

window.Vue = require('vue');
window.moment = require('moment');

import Echo from "laravel-echo"

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '32c16f87fb0b8b82d4d2',
    cluster: 'ap2',
    encrypted: true
});


import Datepicker from 'vuejs-datepicker';
import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);
import 'bootstrap-vue/dist/bootstrap-vue.css'


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Market from './lib/Market';
import UserMarketRequest from './lib/UserMarketRequest';
import UserMarket from './lib/UserMarket';
import UserMarketNegotiation from './lib/UserMarketNegotiation';

// datepicker
Vue.component('Datepicker', Datepicker);


Vue.component('user-header', require('./components/UserHeaderComponent.vue'));

// Market Tab Components
Vue.component('market-group', require('./components/MarketGroupComponent.vue'));
Vue.component('market-tab', require('./components/MarketTabComponent.vue'));

// Interaction Bar Component + children
Vue.component('interaction-bar', require('./components/InteractionBarComponent.vue'));
    Vue.component('ibar-user-market-title', require('./components/InteractionBar/Components/UserMarketTitle.vue'));
    Vue.component('ibar-negotiation-history-contracts', require('./components/InteractionBar/TradeComponents/NegotiationHistoryContracts.vue'));
    Vue.component('ibar-negotiation-history-market', require('./components/InteractionBar/MarketComponents/NegotiationHistoryMarket.vue'));
    Vue.component('ibar-market-negotiation-contracts', require('./components/InteractionBar/MarketComponents/MarketNegotiationMarket.vue'));
    Vue.component('ibar-apply-conditions', require('./components/InteractionBar/MarketComponents/ApplyConditionsComponent.vue'));
    Vue.component('ibar-apply-premium-calculator', require('./components/InteractionBar/MarketComponents/ApplyPremiumCalculatorComponent.vue'));

// Action Bar Component
Vue.component('action-bar', require('./components/ActionBarComponent.vue'));

Vue.component('user-header', require('./components/UserHeaderComponent.vue'));
Vue.component('chat-bar', require('./components/ChatBarComponent.vue'));

// Profile Components
Vue.component('email-settings', require('./components/Profile/Components/EmailSettingsComponent.vue'));
Vue.component('activate-input', require('./components/Profile/Components/ActivateInputComponent.vue'));
Vue.component('toggle-input', require('./components/Profile/Components/ToggleInputComponent.vue'));
Vue.component('day-month-picker', require('./components/Profile/Components/DayMonthPickerComponent.vue'));

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
        },
        loadMarketTypes() {
            let self = this;
            return axios.get(axios.defaults.baseUrl + '/trade/market-type')
            .then(marketTypeResponse => {
                if(marketTypeResponse.status == 200) {
                    // set the available market types
                    self.market_types = marketTypeResponse.data;
                } else {
                    console.error(err);    
                }
                return self.market_types;
            }, err => {
                console.error(err);
            });
        },
        loadMarkets(marketType) {
            let self = this;
            return axios.get(axios.defaults.baseUrl + '/trade/market-type/'+marketType.id+'/market')
            .then(marketResponse => {
                if(marketResponse.status == 200) {
                    if(!marketType.markets) {
                        marketType.markets = [];
                    }
                    marketResponse.data = marketResponse.data.map(x => {
                        x = new Market(x);
                        marketType.markets.push(x);   
                        self.display_markets.push(x);
                        return x;
                    });
                    self.reorderDisplayMarkets(self.display_markets);
                    return marketResponse.data;
                } else {
                    console.error(err);
                }
            });
        },
        loadMarketRequests(market) {
            let self = this;
            console.log("Load Market Request", market);
            return axios.get(axios.defaults.baseUrl + '/trade/market/'+market.id+'/market-request')
            .then(marketResponse => {
                if(marketResponse.status == 200) {
                    marketResponse.data = marketResponse.data.map(x => new UserMarketRequest(x));
                    market.addMarketRequests(marketResponse.data);
                    console.log("Market Requests", marketResponse.data);
                    return marketResponse.data;
                } else {
                    console.error(err);
                }
            });
        },
        loadConfig(config_name, config_file) {
            let self = this;
            return axios.get(axios.defaults.baseUrl + '/config/'+config_file)
            .then(configResponse => {
                if(configResponse.status == 200) {
                    // proxy through vue logic
                    self.configs[config_name] = configResponse.data;
                    return configResponse.data;
                } else {
                    console.error(err);
                }
            });
        },
        config(path) {
            return path.split('.').reduce((acc, cur) => {
                if(acc && typeof acc[cur] !== 'undefined') {
                    return acc[cur];
                }
                return undefined;
            }, this.configs);
        },
        reloadMarketRequests() {
            this.display_markets.forEach(market => {
                this.loadMarketRequests(market);
            });
        },
        addUserMarketRequest(UserMarketRequestData) {
            let index = this.display_markets.findIndex( display_market => display_market.id == UserMarketRequestData.market_id);
            if(index !== -1)
            {
                 this.display_markets[index].addMarketRequest(new UserMarketRequest(UserMarketRequestData));
            }
        }

    },
    data: {
        // default data
        market_order:['TOP 40','DTOP','DCAP','SINGLES','DELTA ONE'],
        no_cares: [],
        display_markets: [],
        market_types: [],

        // internal properties
        configs: {}
    },
    mounted: function() {
        // load config files
        this.loadConfig("trade_structure", "trade_structure.json")
        .catch(err => {
            console.error(err);
            // @TODO: handle this with critical failure... no config = no working trade screen
        })
        .then(configs => {
            // laod the trade data
            this.loadMarketTypes()
            .then(market_types => {
                let promises = [];
                market_types.forEach(market_type => {
                    promises.push(
                        this.loadMarkets(market_type)
                        .then(markets => {
                            markets.forEach(market => {
                                promises.push(
                                    this.loadMarketRequests(market)
                                );
                            });
                        })
                    );
                });
                return Promise.all(promises);
            })
            .then(all_market_requests => {
                // nada
            });
        });
        
        if(Laravel.organisationUuid)
        {
            window.Echo.private('organisation.'+Laravel.organisationUuid)
            .listen('UserMarketRequested', (UserMarketRequest) => {
                //this should be the market thats created
                this.addUserMarketRequest(UserMarketRequest);
            }); 
        }

       

    }
});




// test code
// import emulation from './emulate';
// emulation.setApp(app);
// emulation.init();