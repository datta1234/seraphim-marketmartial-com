
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./components/data-methods');

window.Vue = require('vue');
window.moment = require('moment');

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

import { TradeStructure } from './config/index';
console.log(TradeStructure);

// datepicker
Vue.component('Datepicker', Datepicker);


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

// Action Bar Component
Vue.component('action-bar', require('./components/ActionBarComponent.vue'));

Vue.component('user-header', require('./components/UserHeaderComponent.vue'));
Vue.component('chat-bar', require('./components/ChatBarComponent.vue'));

// Profile Components
Vue.component('email-settings', require('./components/Profile/Components/EmailSettingsComponent.vue'));
Vue.component('activate-input', require('./components/Profile/Components/ActivateInputComponent.vue'));
Vue.component('toggle-input', require('./components/Profile/Components/ToggleInputComponent.vue'));

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
        loadMarkets() {
            let self = this;
            axios.get('/trade/market-type')
            .then(marketTypeResponse => {
                console.log("Market Types", marketTypeResponse);
                if(marketTypeResponse.status == 200) {
                    // set the available market types
                    self.market_types = marketTypeResponse.data;
                    self.market_types.forEach(marketType => {
                        axios.get('/trade/market-type/'+marketType.id+'/market')
                        .then(marketResponse => {
                            if(marketResponse.status == 200) {
                                marketResponse.data.forEach((el) => {
                                    self.display_markets.push(new Market(el));
                                });
                                self.reorderDisplayMarkets(self.display_markets);
                                console.log("Markets", self.display_markets);
                            } else {
                                console.error(err);
                            }
                        });
                    });
                    
                } else {
                    console.error(err);    
                }
            }, err => {
                console.error(err);
            });
        }
    },
    data: {
        // default data
        market_order:['TOP 40','DTOP','DCAP','SINGLES','DELTA ONE'],
        no_cares: [],
        display_markets: [],
        market_types: [],
    },
    mounted: function() {
        this.loadMarkets();
    }
});


// test code
// import emulation from './emulate';
// emulation.setApp(app);
// emulation.init();