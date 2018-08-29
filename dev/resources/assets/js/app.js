
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
    key: 'e86956c85e44edbfbc9c',
    cluster: 'ap2',
    encrypted: true,
    authEndpoint: window.axios.defaults.baseUrl + '/broadcasting/auth',
});


import Datepicker from 'vuejs-datepicker';
import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import 'bootstrap-vue/dist/bootstrap-vue.css'

/**
 * Load the Fontawesome vue component then the library and lastly import
 * and register the icons you want to use.
 */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCheck, faCheckDouble } from '@fortawesome/free-solid-svg-icons';

library.add(faCheck,faCheckDouble);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Market from './lib/Market';
import UserMarketRequest from './lib/UserMarketRequest';
import UserMarket from './lib/UserMarket';
import UserMarketNegotiation from './lib/UserMarketNegotiation';
import Message from './lib/Message';

import { EventBus } from './lib/EventBus.js';

// datepicker
Vue.component('Datepicker', Datepicker);

Vue.component('VuePerfectScrollbar', VuePerfectScrollbar);

Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.component('user-header', require('./components/UserHeaderComponent.vue'));

Vue.component('mm-loader', require('./components/LoaderComponent.vue'));

Vue.component('theme-toggle', require('./components/ThemeToggleComponent.vue'));

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
                    return sbl + this.splitValHelper( calcVal, ' ', 3);
            }
        },
        /**
         * Takes in a value and splits the value by a splitter in a desired frequency
         *
         * @param {string|number} val - the desired value to split
         * @param {string} splitter - the splitter to split the value by
         * @param {number} frequency - the frequency in which to apply the split to the value
         *
         * @return {string} the newly splitted value
         */
        splitValHelper (val, splitter, frequency) {
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
        },
        dateStringArraySort(date_string_array, format) {
            for(let i = 0; i < date_string_array.length - 1; i++) {
                for(let j = 0; j < date_string_array.length - i - 1; j++) {
                    if( moment(date_string_array[j+1],format).isBefore(moment(date_string_array[j],format)) ) {
                        let temp = date_string_array[j];
                        date_string_array[j] = date_string_array[j+1];
                        date_string_array[j+1] = temp;
                    }
                }
            }
        },
    }
});

const app = new Vue({
    el: '#trade_app',
    computed: {
        tradeTheme: function() {
            return this.theme_toggle ? 'light-theme' : 'dark-theme';
        }
    },
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
        loadNoCares(){
                 if (localStorage.getItem('no_cares_market_request')) {
                  try {
                    this.no_cares = JSON.parse(localStorage.getItem('no_cares_market_request'));
                  } catch(e) {
                    localStorage.removeItem('no_cares_market_request');
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
            return axios.get(axios.defaults.baseUrl + '/trade/market/'+market.id+'/market-request')
            .then(marketResponse => {
                if(marketResponse.status == 200) {
                    marketResponse.data = marketResponse.data.map(x => new UserMarketRequest(x));
                    market.addMarketRequests(marketResponse.data);
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
        loadUserConfig() {
            let self = this;
            return axios.get(axios.defaults.baseUrl + '/user-pref')
            .then(configResponse => {
                if(configResponse.status == 200) {
                    self.configs["user_preferences"] = configResponse.data;
                    return configResponse.data;
                } else {
                    self.configs["user_preferences"] = null;
                    console.error(err);
                }
                return self.configs["user_preferences"];
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
        updateUserMarketRequest(UserMarketRequestData) {
            let index = this.display_markets.findIndex( display_market => display_market.id == UserMarketRequestData.market_id);
            if(index !== -1)
            {
                let request_index = this.display_markets[index].market_requests.findIndex( market_request => market_request.id == UserMarketRequestData.id);
                if(request_index !== -1) {
                    this.display_markets[index].updateMarketRequest(UserMarketRequestData, request_index);
                } else {
                    this.display_markets[index].addMarketRequest(new UserMarketRequest(UserMarketRequestData));
                }
            } else {
                //@TODO: Add logic to display market if not already displaying
            }
        },
        loadThemeSetting() {
            if (localStorage.getItem('themeState') != null) {
                try {
                    this.theme_toggle = localStorage.getItem('themeState') === 'true';
                } catch(e) {
                    this.theme_toggle = false;
                    localStorage.removeItem('themeState');
                }
            } else {
                this.theme_toggle = false;
                try {
                    localStorage.setItem('themeState', this.theme_toggle);
                } catch(e) {
                    localStorage.removeItem('themeState');
                }
            }
        },
        setThemeState(state) {
            this.theme_toggle = state;
            try {
                localStorage.setItem('themeState', this.theme_toggle);
            } catch(e) {
                localStorage.removeItem('themeState');
            }
        },
        handlePacket(chunk_data) {
            // Clears expired completed messages
            this.clearExpiredMessages(chunk_data);

            let completed_index = this.completed_messages.findIndex( (message) => {
                return ( message.checksum == chunk_data.checksum);
            });

            if(completed_index === -1) {
                // check if the message is already in this.pusher_messages
                let index = this.pusher_messages.findIndex( (message) => {
                    return ( message.checksum == chunk_data.checksum && message.total == chunk_data.total && message.expires.isSame(chunk_data.expires) );
                });
                
                if(index !== -1) {
                // if so then just add new packet
                    this.pusher_messages[index].addChunk(chunk_data);
                } else {
                // if not create new message and then add chunk
                    let message = new Message({'checksum': chunk_data.checksum, 'total': chunk_data.total, 'expires': chunk_data.expires});
                    message.addChunk(chunk_data);
                    this.pusher_messages.push(message);
                }

                // unpack data if the message is complete
                let unpacked_data;
                if(index !== -1) {
                    unpacked_data = this.pusher_messages[index].getUnpackedData(); 
                } else {
                    unpacked_data = this.pusher_messages[this.pusher_messages.length -1].getUnpackedData();  
                }
                if(unpacked_data !== null) {
                    // remove completed messages and add them to completed
                    let completed_message;
                    if (index !== -1) {
                        completed_message = this.pusher_messages.splice(index, 1); 
                    } else {
                        completed_message = this.pusher_messages.splice(this.pusher_messages.length -1, 1);
                    }   
                    this.completed_messages.push({checksum : completed_message[0].checksum,timestamp : completed_message[0].timestamp});
                    this.updateUserMarketRequest(unpacked_data);
                }
            }
        },
        clearExpiredMessages(chunk_data) {
            this.completed_messages.forEach( (message, index) => {
                if(message.timestamp.isBefore(chunk_data.timestamp)) {
                    this.completed_messages.splice(index, 1);
                }
            });  
        },
    },
    data: {
        // default data
        market_order:['TOP 40','DTOP','DCAP','SINGLES','DELTA ONE'],
        no_cares: [],
        display_markets: [],
        hidden_markets: [],
        market_types: [],
        message_count: 0,
        page_loaded: false,
        // internal properties
        configs: {},
        theme_toggle: false,
        pusher_messages: [
            new Message({'checksum': 'eyJpZCI6MTIsIm1hcmtldF9pZCI6MSwiaXNfaW50ZR4363qRR', 'total': 8, 'expires': '2018-08-16 00:00:00'}),
            new Message({'checksum': 'eyJpZCI6MTIsIm1hcmtldF9pZCI6MSwiaXNf58Tr5ipL90', 'total': 8, 'expires': '2018-08-16 00:00:00'}),
            new Message({'checksum': 'eyJpZCI6MTIsIm1hcmtldF9pZCI6MSwiaXNfaW50ZXJlc3QiOnRydW', 'total': 4, 'expires': '2018-08-16 00:00:00'})
        ],
        completed_messages: [],
    },
    mounted: function() {
        // get Saved theme setting
        this.loadThemeSetting();
        // load config files
        this.loadConfig("trade_structure", "trade_structure.json")
        .catch(err => {
            console.error(err);
            // @TODO: handle this with critical failure... no config = no working trade screen
        })
        .then( () => {
            this.loadConfig('condition_titles','condition_titles.json');
            this.loadUserConfig();
        })
        .then(configs => {
            // load the trade data
            this.loadMarketTypes();
            this.loadUserConfig()
            .then(user_preferences => {
                let promises = [];
                if(user_preferences !== null) {
                    user_preferences.prefered_market_types.forEach(market_type => {
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
                }
                return Promise.all(promises);
            })
            .then(all_market_requests => {
                EventBus.$emit('loading', 'page');
                this.page_loaded = true;
                //load the no cares from storage
                this.loadNoCares();
            });
        });
        
        if(Laravel.organisationUuid)
        {
            window.Echo.private('organisation.'+Laravel.organisationUuid)
            .listen('UserMarketRequested', (UserMarketRequest) => {
                //this should be the market thats created
                console.log("this is what websockets is",UserMarketRequest);
                this.updateUserMarketRequest(UserMarketRequest);

                // @TODO - move above logic to decode section in handlepacket logic
                // this.handlePacket(UserMarketRequest);
            })
            .listen('ChatMessageReceived', (received_org_message) => {
                this.$emit('chatMessageReceived', received_org_message);
            }); 
        }

        EventBus.$on('toggleTheme', this.setThemeState);

        // Sample data for Pusher Message Packages
        /*let test_data1 = {
            checksum: 'cLOfXrpHrLVzopi3qdWjKnSR9Dt6kvGNGKIzb7k5jHg=',
            packet: 1,
            total: 4,
            data: 'eyJpZCI6MTIsIm1hcmtldF9pZCI6MSwiaXNfaW50ZXJlc3QiOnRydWUsImlzX21hcmtldF9tYWtlciI6ZmFsc2UsInRyYWRlX3N0cnVjdHVyZSI6Ik91dHJpZ2h0IiwidHJhZGVfaXRlbXMiOnsiZGVmYXVsdCI6eyJFeHBpcmF0aW9uIERhdGUiOiJKdW4xOSIsIlN0cmlrZSI6IjMxNjU0NjQiLCJRdWFudGl0eSI6IjUwMCJ9fSwiYXR0cmlidXRlcyI6eyJzdGF0ZSI6IlJFUVVFU1QtU0VOVC1WT0wiLCJiaWRfc3RhdGUiOiJhY3Rpb24iLCJvZmZlcl9zdGF0ZSI6ImFjdGlvbiIsImFjdGlvbl9uZWVkZWQiOnRydWV9LCJjcmVhdGVkX2F0IjoiMjAxOC0wOC0yNyA',
            expires: '2018-08-16 00:00:00',
            timestamp:'2018-08-16 00:00:00'
        };
        let test_data2 = {
            checksum: 'cLOfXrpHrLVzopi3qdWjKnSR9Dt6kvGNGKIzb7k5jHg=',
            packet: 2,
            total: 4,
            data: 'wODo1MToxOCIsInVwZGF0ZWRfYXQiOiIyMDE4LTA4LTI3IDA4OjUxOjE4Iiwic2VudF9xdW90ZSI6eyJpZCI6MTAsInVzZXJfbWFya2V0X3JlcXVlc3RfaWQiOjEyLCJjdXJyZW50X21hcmtldF9uZWdvdGlhdGlvbl9pZCI6MTAsImlzX3RyYWRlX2F3YXkiOmZhbHNlLCJpc19tYXJrZXRfbWFrZXJfbm90aWZpZWQiOmZhbHNlLCJjcmVhdGVkX2F0IjoiMjAxOC0wOC0yOCAwOToxMToxOCIsInVwZGF0ZWRfYXQiOiIyMDE4LTA4LTI4IDA5OjExOjE4IiwiZGVsZXRlZF9hdCI6bnVsbCwiaXNfb25faG9sZCI6ZmFsc2UsImN1cnJlbnRfbWFya2V0X25lZ290aWF0aW',
            expires: '2018-08-16 00:00:00',
            timestamp:'2018-08-16 00:00:00'
        };
        let test_data3 = {
            checksum: 'cLOfXrpHrLVzopi3qdWjKnSR9Dt6kvGNGKIzb7k5jHg=',
            packet: 3,
            total: 4,
            data: '9uIjp7ImlkIjoxMCwibWFya2V0X25lZ290aWF0aW9uX2lkIjpudWxsLCJ1c2VyX21hcmtldF9pZCI6MTAsImJpZCI6MTUsIm9mZmVyIjoxNiwiYmlkX3F0eSI6NTAwLCJvZmZlcl9xdHkiOjUwMCwiYmlkX3ByZW1pdW0iOm51bGwsIm9mZmVyX3ByZW1pdW0iOm51bGwsImZ1dHVyZV9yZWZlcmVuY2UiOm51bGwsImhhc19wcmVtaXVtX2NhbGMiOjAsImlzX3JlcGVhdCI6MCwiaXNfYWNjZXB0ZWQiOjAsImlzX3ByaXZhdGUiOjEsImNvbmRfaXNfcmVwZWF0X2F0dyI6bnVsbCwiY29uZF9mb2tfYXBwbHlfYmlkIjpudWxsLCJjb25kX2Zva19zcGluIjpudWxsLCJjb',
            expires: '2018-08-16 00:00:00',
            timestamp:'2018-08-16 00:00:00'
        };
        let test_data4 = {
            checksum: 'cLOfXrpHrLVzopi3qdWjKnSR9Dt6kvGNGKIzb7k5jHg=',
            packet: 4,
            total: 4,
            data: '25kX3RpbWVvdXQiOm51bGwsImNvbmRfaXNfb2NkIjpudWxsLCJjb25kX2lzX3N1YmplY3QiOm51bGwsImNvbmRfYnV5X21pZCI6bnVsbCwiY29uZF9idXlfYmVzdCI6bnVsbCwiY3JlYXRlZF9hdCI6IjIwMTgtMDgtMjggMDk6MTE6MTgiLCJ1cGRhdGVkX2F0IjoiMjAxOC0wOC0yOCAwOToxMToxOCIsInRpbWUiOiIwOToxMSJ9fSwicXVvdGVzIjpbeyJpZCI6MTAsImlzX2ludGVyZXN0Ijp0cnVlLCJpc19tYWtlciI6dHJ1ZSwiYmlkX29ubHkiOmZhbHNlLCJvZmZlcl9vbmx5IjpmYWxzZSwidm9sX3NwcmVhZCI6MSwidGltZSI6IjA5OjExIiwiYmlkIjoxNSwib2ZmZXIiOjE2LCJiaWRfcXR5Ijo1MDAsIm9mZmVyX3F0eSI6NTAwLCJpc19yZXBlYXQiOjAsImlzX29uX2hvbGQiOmZhbHNlfV19',
            expires: '2018-08-16 00:00:00',
            timestamp:'2018-08-16 00:00:00'
        };
        this.handlePacket(test_data1);
        this.handlePacket(test_data2);
        this.handlePacket(test_data3);
        this.handlePacket(test_data4);*/
    }
});




// test code
// import emulation from './emulate';
// emulation.setApp(app);
// emulation.init();