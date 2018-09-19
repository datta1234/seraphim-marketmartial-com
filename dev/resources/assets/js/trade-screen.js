
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


import Datepicker from 'vuejs-datepicker'
import BootstrapVue from 'bootstrap-vue'
import Toasted from 'vue-toasted'
Vue.use(BootstrapVue)
Vue.use(Toasted, {
    position: 'top-center',
    fullWidth: false,
    action: {
        text: 'Dismiss',
        onClick(e, t) {
            t.goAway(0);
        }
    },
    theme: 'primary'
})
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
import Message from './lib/Message'

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
    Vue.component('ibar-trade-request', require('./components/InteractionBar/TradeComponents/TradeRequestComponent.vue'));

// Action Bar Component
Vue.component('action-bar', require('./components/ActionBarComponent.vue'));

Vue.component('user-header', require('./components/UserHeaderComponent.vue'));
Vue.component('chat-bar', require('./components/ChatBarComponent.vue'));

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
        /*
         * Basic bubble sort that sorts a date string array usesing Moment.
         *
         * @param {String[]} date_string_array - array of date string
         * @param {String} format - the format to cast to a moment object
         */
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
            console.log("I NEVER GET CALLED",this.theme_toggle);
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
        loadConfigs(config_list) {
            let promises = [];
            config_list.forEach(config => {
                console.log(config)
                promises.push(this.loadConfig.apply(this, config.constructor === Array ? config : [config]));
            });
            return Promise.all(promises);
        },
        /**
         * Makes an axios get request to get the user preferences         
         *
         * @return {Object} - the config response data
         */
        loadConfig(config_name, config_file) {
            let self = this;
            config_file = (typeof config_file !== 'undefined' ? config_file : config_name+".json");
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
        /**
         * Updates User Market Request based on the UserMarketRequestData object passed.
         *  Finds the Market Request in display markets and updates or adds the Market Request          
         *
         * @param {Object} - User market request data object
         * 
         * @todo - Add logic to display market if not already displaying
         */
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
        /**
         * Loads user prefered theme setting base on local storage variable         
         */
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
        /**
         * Toggles theme state based on a passed state param and saves it to local storage
         *
         * @param {Boolean} state         
         */
        setThemeState(state) {
            this.theme_toggle = state;
            try {
                localStorage.setItem('themeState', this.theme_toggle);
            } catch(e) {
                localStorage.removeItem('themeState');
            }
        },
        /**
         * Handles incoming new message chunks and unpacks new data when a message has been completed
         *
         * @param {Object} chunk_data - new chunk packet data         
         */
        handlePacket(chunk_data, callback) {
            // Clears expired completed messages
            this.clearExpiredMessages(chunk_data);

            // Check if the message has already been completed in this.completed_messages
            let message = this.completed_messages.find( (msg_val) => {
                return ( msg_val.checksum == chunk_data.checksum);
            });
            if(typeof message !== 'undefined') {
                // Break if there is already a completed message for this checksum
                return;
            }

            // check if the message is already in this.pusher_messages
            message = this.pusher_messages.find( (msg_val) => {
                // is valid if the checksum exists
                return msg_val.checksum == chunk_data.checksum;
            });
            if(typeof message === 'undefined') {
                // if its not being tracked, track a new one
                message = new Message({'checksum': chunk_data.checksum, 'total': chunk_data.total, 'expires': chunk_data.expires}, (err, output_message) => {
                    // if the message is complete, attempt completion callback
                    if(!err) {                        
                        // pull the message out of current and into completed
                        let completed_message = this.pusher_messages.splice(this.pusher_messages.indexOf(output_message), 1);  
                        this.completed_messages.push({checksum : completed_message[0].checksum,expires : completed_message[0].expires});
                        // run the message callback
                        callback(output_message.output);
                    } else {
                        console.error("derp");
                    }
                });
                this.pusher_messages.push(message);
            }
            
            // add chunk data to message
            message.addChunk(chunk_data);
        },
        /**
         * Removes all completed messages that have expired
         *
         * @param {Object} chunk_data - new chunk packet data         
         */
        clearExpiredMessages(chunk_data) {
            this.completed_messages.forEach( (message, index) => {
                if(message.expires.isBefore(chunk_data.timestamp)) {
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
        pusher_messages: [],
        completed_messages: [],
    },
    mounted: function() {
        // get Saved theme setting
        this.loadThemeSetting();
        // load config files
        this.loadConfigs([
            ["trade_structure","trade_structure.json"],     // [ <namespace> , <file_path> ]
            ["condition_titles"],                           // [ <namespace> ] ( assumes fileanme == <namespace>.json )
            "market_conditions",                            //   <namespace> ( same assumption as above )
        ])
        .catch(err => {
            console.error(err);
            // @TODO: handle this with critical failure... no config = no working trade screen
        })
        .then(this.loadUserConfig)
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
            .listen('.UserMarketRequested', (userMarketRequest) => {
                //this should be the market thats created
                this.handlePacket(userMarketRequest, (packet_data) => {
                    console.log("publish Callback", packet_data);
                    this.updateUserMarketRequest(packet_data.data);
                    EventBus.$emit('notifyUser',{"user_market_request_id":packet_data.data.id,"message":packet_data.message });
                });
            })
            .listen('ChatMessageReceived', (received_org_message) => {
                this.$emit('chatMessageReceived', received_org_message);
            }); 
        }
        // Event listener that listens for theme toggle event to keep track of theme state
        EventBus.$on('toggleTheme', this.setThemeState);
    }
});




// test code
// import emulation from './emulate';
// emulation.setApp(app);
// emulation.init();