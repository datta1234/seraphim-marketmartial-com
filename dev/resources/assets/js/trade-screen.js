
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./components/data-methods');

window.Vue = require('vue');
window.moment = require('moment');
const momentDuration = require('moment-duration-format');
momentDuration(moment);

window.math = require('mathjs');

import Echo from "laravel-echo"

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: document.head.querySelector('meta[name="pusher-key"]').content,
    cluster: document.head.querySelector('meta[name="pusher-cluster"]').content,
    encrypted: true,
    authEndpoint: window.axios.defaults.baseUrl + '/broadcasting/auth',
});

/**
 *  Sentry for tracking and logging client issues
 */
import * as Sentry from "@sentry/vue";
import { Integrations } from "@sentry/tracing";

Sentry.init({
    Vue,
    dsn: "https://1bd4f21d4e514da398cd8552d50146c6@sentry.exonic.co.za/29",
    integrations: [
        new Integrations.BrowserTracing({}),
    ],
    // Set tracesSampleRate to 1.0 to capture 100%
    // of transactions for performance monitoring.
    // We recommend adjusting this value in production
    tracesSampleRate: 0.3,
    logErrors: true
});



import Datepicker from 'vuejs-datepicker';
import BootstrapVue from 'bootstrap-vue';
import Toasted from 'vue-toasted';
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
    theme: 'primary',
    duration : 3000,
})
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import 'bootstrap-vue/dist/bootstrap-vue.css';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Config from './lib/Config';
import Market from './lib/Market';
import UserMarketRequest from './lib/UserMarketRequest';
import UserMarket from './lib/UserMarket';
import UserMarketNegotiation from './lib/UserMarketNegotiation';
import Message from './lib/Message'
import TradeConfirmation from './lib/TradeConfirmation'

import { EventBus } from './lib/EventBus.js';

// datepicker
Vue.component('Datepicker', Datepicker);

Vue.component('trading-countdown', require('./components/PreviousDay/TradingCountdown.vue').default);

Vue.component('VuePerfectScrollbar', VuePerfectScrollbar);

Vue.component('user-header', require('./components/UserHeaderComponent.vue').default);

Vue.component('mm-loader', require('./components/LoaderComponent.vue').default);

Vue.component('theme-toggle', require('./components/ThemeToggleComponent.vue').default);
Vue.component('notification-toggle', require('./components/NotificationToggleComponent.vue').default);

// Market Tab Components
Vue.component('market-group', require('./components/MarketGroupComponent.vue').default);
Vue.component('market-tab', require('./components/MarketTabComponent.vue').default);

// Interaction Bar Component + children
Vue.component('interaction-bar', require('./components/InteractionBarComponent.vue').default);
    Vue.component('ibar-user-market-title', require('./components/InteractionBar/Components/UserMarketTitle.vue').default);
    Vue.component('ibar-negotiation-history-contracts', require('./components/InteractionBar/TradeComponents/NegotiationHistoryContracts.vue').default);
    Vue.component('ibar-negotiation-history-market', require('./components/InteractionBar/MarketComponents/NegotiationHistoryMarket.vue').default);
    Vue.component('ibar-market-negotiation-contracts', require('./components/InteractionBar/MarketComponents/MarketNegotiationMarket.vue').default);
    Vue.component('ibar-apply-conditions', require('./components/InteractionBar/MarketComponents/ApplyConditionsComponent.vue').default);
    Vue.component('ibar-apply-premium-calculator', require('./components/InteractionBar/MarketComponents/ApplyPremiumCalculatorComponent.vue').default);
    
    Vue.component('ibar-trade-request', require('./components/InteractionBar/TradeComponents/TradeRequest.vue').default);
    Vue.component('ibar-trade-desired-quantity', require('./components/InteractionBar/TradeComponents/TradeDesiredQuantity.vue').default);
    Vue.component('ibar-trade-counter-desired-quantity', require('./components/InteractionBar/TradeComponents/TradeCounterDesiredQuantity.vue').default);
    Vue.component('ibar-trade-work-balance', require('./components/InteractionBar/TradeComponents/TradeWorkBalance.vue').default);
    Vue.component('ibar-counter-negotiation', require('./components/InteractionBar/MarketComponents/CounterNegotiation.vue').default);

// Action Bar Component
Vue.component('action-bar', require('./components/ActionBarComponent.vue').default);

Vue.component('user-header', require('./components/UserHeaderComponent.vue').default);
Vue.component('chat-bar', require('./components/ChatBarComponent.vue').default);
Vue.component('refresh-quotes-modal', require('./components/PreviousDay/RefreshQuotesModal.vue').default);
Vue.component('trade-closing-timer', require('./components/TradeClosingComponent.vue').default);

// directives
import ActiveRequestDirective from './directives/active-request.js';
import InputMask from './directives/input-mask.js';
Vue.directive('active-request', ActiveRequestDirective);
Vue.directive('input-mask', InputMask);

import ActiveMakerService from '~/services/ActiveMakerService';
import ActiveMarketMakers from './components/ActiveMarketMakers.vue'
Vue.component('active-makers', ActiveMarketMakers);

import FormatMixin from './FormatMixin.js'
Vue.mixin(FormatMixin);

const app = new Vue({
    el: '#trade_app',
    computed: {
        tradeTheme: function() {
            
            // toggle on html tag too
            document.documentElement.classList.remove( this.theme_toggle ? 'dark' : 'light' );
            document.documentElement.classList.add( this.theme_toggle ? 'light' : 'dark' );

            return this.theme_toggle ? 'light-theme' : 'dark-theme';
        }
    },
    watch: {
        'display_markets': function(nv, ov) {
            this.reorderDisplayMarkets(nv);
        }
    },
    methods: {
        updateServerTimes() {
            let self = this;
            return axios.get(axios.defaults.baseUrl + '/trade')
            .then(serverTimesResponse => {
                if(serverTimesResponse.status == 200) {
                    // Emit server times updated event to notify components to read new times
                    if(serverTimesResponse.data) {
                        self.server_time = serverTimesResponse.data.server_time; 
                        self.closing_time = serverTimesResponse.data.closing_time; 
                        self.trade_start = serverTimesResponse.data.trade_start; 
                    }
                    self.$emit('serverTimesUpdated');
                } else {
                    //console.error(err);    
                }
            }, err => {
                //console.error(err);
            });
        },
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
                    //console.error(err);    
                }
                return self.market_types;
            }, err => {
                //console.error(err);
            });
        },
        removeTradeConfirmations(marketType){
                let self = this;
                let index = self.trade_confirmations.findIndex((item) => {
                    return item.market_type_id == marketType.id;
                });
                if(index != -1) {
                    
                }
        },
        loadTradeConfirmations(marketType) {
            if(this.is_admin || this.is_viewer) {
                return;
            }
            let self = this;
            return axios.get(axios.defaults.baseUrl + '/trade/market-type/'+marketType.id+'/trade-confirmations')
            .then(tradeConfirmationResponse => {
                // set the available market types
                tradeConfirmationResponse.data.data = tradeConfirmationResponse.data.data.map(x => {
                                      
                   self.trade_confirmations.push(new TradeConfirmation(x));   
                    return x;
                });
                return self.trade_confirmations;
            }, err => {
                //console.error(err);
            });
        },
        loadMarkets(marketType) {
            let self = this;
            return axios.get(axios.defaults.baseUrl + '/trade/market-type/'+marketType.id+'/market')
            .then(marketResponse => {
                if(marketResponse.status == 200) {
                    marketResponse.data = marketResponse.data.map(x => {
                        x = new Market(x);   
                        self.display_markets.push(x);
                        return x;
                    });
                    self.reorderDisplayMarkets(self.display_markets);
                    return marketResponse.data;
                } else {
                    //console.error(err);
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
                    //console.error(err);
                }
            });
        },
        loadConfigs(config_list) {
            let promises = [];
            config_list.forEach(config => {
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
                    //console.error(err);
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
                    //console.error(err);
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
        updateUserMarketRequest(userMarketRequestData) {

            // @TODO: ONLY update the found record, IF message.timestamp > current request.timestamp_recieved
            // + add .timestamp_recieved field to marketRequests

            // handle removal of market requests
            if(typeof userMarketRequestData.inactive !== 'undefined' && userMarketRequestData.inactive == true) {
                let market = this.display_markets.find(display_market => display_market.id == userMarketRequestData.market_id);
                if(market) {
                    let market_request_index = market.market_requests.findIndex( market_request => market_request.id == userMarketRequestData.id);
                    if(market_request_index !== -1) {
                        market.market_requests.splice(market_request_index, 1); // remove
                        EventBus.$emit('removeMarketRequest', userMarketRequestData.id);
                    }
                }
                return; // return early
            }
            //handle adding/updating
            let index = this.display_markets.findIndex( display_market => display_market.id == userMarketRequestData.market_id);
            if(index !== -1)
            {
                let request_index = this.display_markets[index].market_requests.findIndex( market_request => market_request.id == userMarketRequestData.id);
                if(request_index !== -1) {
                    this.display_markets[index].updateMarketRequest(userMarketRequestData, request_index);
                } else {
                    this.display_markets[index].addMarketRequest(new UserMarketRequest(userMarketRequestData));
                    this.$emit('audioNotify');
                }
            } else {
                //@TODO: Add logic to display market if not already displaying
            }
        },
        updateTradeConfirmation(tradeConfirmationData){
            let index = this.display_markets.findIndex(display_market => display_market.id == tradeConfirmationData.market_id);
        
            if(index !== -1)
            {
                let trade_confirmation_index = this.trade_confirmations.findIndex( trade_confirmation => trade_confirmation.id == tradeConfirmationData.id);
                let root_confirmation_index = this.trade_confirmations.findIndex( trade_confirmation => trade_confirmation.root_id == tradeConfirmationData.root_id);
                
                let is_old_confirmation = false;
                this.trade_confirmations.forEach(trade_confirmation => {
                    is_old_confirmation = is_old_confirmation || (trade_confirmation.id >= tradeConfirmationData.id);
                });

                if(trade_confirmation_index !== -1) {
                    if(!tradeConfirmationData.can_interact) {
                        // User can no longer interact with this confirmation and thus should not have it
                        this.trade_confirmations.splice(trade_confirmation_index,1);

                    } else {
                        /*
                            Got a new packet for existing confo and updates the confirmation
                                should no longer hit but is left in as possible error handleing for the scenario
                        */
                        this.trade_confirmations[trade_confirmation_index].update(tradeConfirmationData);
                    }
                } else if(root_confirmation_index !== -1) {
                    if(!tradeConfirmationData.can_interact) {
                        // User can no longer interact with this confirmation and thus should not have it
                        this.trade_confirmations.splice(root_confirmation_index,1);
                    } else {
                        // Updated phase creates a new confo record and replaces the old one
                        this.trade_confirmations.splice(root_confirmation_index,1, new TradeConfirmation(tradeConfirmationData));
                    }
                } else if(is_old_confirmation) {
                    // Should never get reached but if it does we are handeling it
                    return;
                } else {
                    //only keep the interaction if the user can interact with it
                    if(tradeConfirmationData.can_interact)
                    {
                        this.trade_confirmations.push(new TradeConfirmation(tradeConfirmationData));
                        this.$emit('audioNotify');  
                    }
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
         * add no cares and saves it to local storage
         *
         * @param {Boolean} state         
         */
        addToNoCares(market_request_id)
        {   
          if(!this.no_cares.includes(market_request_id)) {
                this.no_cares.push(market_request_id);
                this.saveNoCares();
            }
        },
        removefromNoCares(market_request_id)
        {
            let index = this.no_cares.indexOf(market_request_id);

              if(index > -1) 
              {
                this.no_cares.splice(index,1);
                this.saveNoCares();
              }

        },
        saveNoCares(){
           const parsed = JSON.stringify(this.no_cares);
           localStorage.setItem('no_cares_market_request', parsed);
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
                return ( msg_val.checksum == chunk_data.checksum );
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
                message = new Message(chunk_data, (err, output_message) => {
                    // if the message is complete, attempt completion callback
                    if(!err) {
                        // pull the message out of current and into completed
                        let completed_message = this.pusher_messages.splice(this.pusher_messages.indexOf(output_message), 1);  
                        this.completed_messages.push({checksum : completed_message[0].checksum,expires : completed_message[0].expires});
                        // run the message callback
                        callback(output_message.output);
                    } else {
                        //console.error(err);
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
        market_order:['TOP 40','CTOP','CTOR','SINGLES','DELTA ONE'],
        no_cares: [],
        display_markets: [],
        hidden_markets: [],
        market_types: [],
        trade_confirmations: [],
        message_count: 0,
        page_loaded: false,
        // internal properties
        configs: {},
        theme_toggle: false,
        pusher_messages: [],
        completed_messages: [],
        scroll_settings: {
            suppressScrollY: true
        },
        is_admin: false,
        is_viewer: false,
        trading_opens: null,
        server_time: null,
        closing_time: null,
        trade_start: null,
    },
    created() {
        let viewer_type = document.head.querySelector('meta[name="viewer-type"]');
        // test if a viewer type user is viewing the page, then disable some send features across the frontend
        if(viewer_type && viewer_type.content) {
            this.is_viewer = true;
        }

        let organisationUuid = document.head.querySelector('meta[name="organisation-uuid"]');
        if(organisationUuid && organisationUuid.content)
        {
            if(organisationUuid.content == "admin") {
                this.is_admin = true;
            }
        }
    },
    mounted: function() {
        this.trading_opens = moment(document.head.querySelector('meta[name="trading-opens"]').content);
        Config.configs = this.configs;
        // get Saved theme setting
        this.loadThemeSetting();
        // load config files
        this.loadConfigs([
            ["trade_structure","trade_structure.json"],     // [ <namespace> , <file_path> ]
            ["condition_titles"],                           // [ <namespace> ] ( assumes fileanme == <namespace>.json )
            "market_conditions",                            //   <namespace> ( same assumption as above )
            "fees",                                         // [ <namespace> , <file_path> ]
            "app",
        ])
        .catch(err => {
            //console.error(err);
            // @TODO: handle this with critical failure... no config = no working trade screen
        })
        .then(configs => {
            // Initialise Hooks
            ActiveRequestDirective.init(app);
            ActiveMakerService.init(app);
            return configs;
        })
        .then(this.loadUserConfig)
        .then(this.loadMarketTypes)
        .then(market_types => {
            let promises = [];
            if(this.configs["user_preferences"] !== null) {
                this.configs["user_preferences"].prefered_market_types.forEach(market_type_id => {
                    let market_type = this.market_types.find(element => {
                        return element.id == market_type_id;
                    });
                    promises.push(this.loadTradeConfirmations(market_type));

                    promises.push(this.loadMarkets(market_type));
                });
            }
            //
            return Promise.all(promises);
        })
        .then(market_types => {
            let promises = [];
            this.display_markets.forEach(market => {
                promises.push(
                    this.loadMarketRequests(market)
                );
            });
            return Promise.all(promises);
        })
        .then(all_loaded => {
            EventBus.$emit('loading', 'page');
            this.page_loaded = true;
            //load the no cares from storage
            this.loadNoCares();

            let market_titles = [];
            this.display_markets.forEach(m => {
                m.market_requests.forEach(mr => {
                    market_titles.push(mr.marketTradeTitle());
                })
            });
        });

        let viewer_type = document.head.querySelector('meta[name="viewer-type"]');
        // test if a viewer type user is viewing the page, then disable some send features across the frontend
        if(viewer_type && viewer_type.content) {
            this.is_viewer = true;
        }

        let organisationUuid = document.head.querySelector('meta[name="organisation-uuid"]');
        if(organisationUuid && organisationUuid.content)
        {
            let handlePusherDisconnect = function(event) {
                //console.error("Pusher failed Event: ", event);
                Sentry.captureException(event, {
                    tags: {
                        section: "pusher-disconnect-handler"
                    }
                });

                let re = confirm("Live update stream disconnected!\n\nIf problem persists, please contact an administrator\nReload Now?");
                if(re) {
                    location.reload();
                }
            };

            let handlePusherError = function(error) {
                Sentry.withScope(function(scope) {
                    Sentry.setContext("Error object string", error.error);
                    Sentry.captureException(error, {
                        tags: {
                            section: "pusher-error-handler"
                        }
                    });
                });
            };

            let handlePusherStateChange = function(state) {
                // @TODO - add handler for states to display
                /*console.log("Pusher state change: ", state);*/
            };

            let connectStream = (subCb) => {
                // test if admin is viewing the page, then disable some send features across the frontend
                if(organisationUuid.content == "admin") {
                    this.is_admin = true;
                }
                // possibly let us cath what happens when pusher dc's
                window.Echo.connector.pusher.connection.bind('disconnected', handlePusherDisconnect);
                window.Echo.connector.pusher.connection.bind('error', handlePusherError);
                window.Echo.connector.pusher.connection.bind('state_change', handlePusherStateChange);

                let channel = window.Echo.private('organisation.'+organisationUuid.content)
                .listen('.UUIDUpdated', (newIdentity) => {
                    // remove bindings
                    window.Echo.connector.pusher.connection.unbind('disconnected', handlePusherDisconnect);
                    // leave old channel
                    window.Echo.leave('organisation.'+organisationUuid.content);
                    // set new UUID and re-connect
                    organisationUuid.setAttribute('content', newIdentity.data);
                    connectStream();

                    // reload markets that may have been missed.
                    this.reloadMarketRequests();
                })
                .listen('.UserMarketRequested', (userMarketRequest) => {
                    //this should be the market thats created
                    this.handlePacket(userMarketRequest, (packet_data) => {
                        this.updateUserMarketRequest(packet_data.data);
                        //@TODO - @Francois Move to new event when rebate gets created
                        if(packet_data.message && packet_data.message.key && packet_data.message.key == "market_traded") {
                            this.$toasted.success(packet_data.message.data, { duration : 20000 });
                        }
                        if(packet_data.message && packet_data.message.key && packet_data.message.key == "no_trade") {
                            this.$toasted.show(packet_data.message.data,{
                                'className':"mm-confirm-toast",
                                duration : 20000,
                            }); 
                        }
                        EventBus.$emit('notifyUser',{"user_market_request_id":packet_data.data.id,"message":packet_data.message });
                    });
                })
                .listen('.TradeConfirmationEvent', (tradeConfirmationPackets) => {
                    //this should be the market thats created
                    this.handlePacket(tradeConfirmationPackets, (packet_data) => {
                        this.updateTradeConfirmation(packet_data.data);
                        if(packet_data.message)
                        {
                            this.$toasted.show(packet_data.message.data,{
                                'className':"mm-confirm-toast",
                                duration : packet_data.message.timer,
                            }); 
                        }
                    });
                })
                .listen('RebateEvent', (rebate) => {
                    if(rebate.message)
                    {
                         this.$toasted.show(rebate.message.data, { 
                            duration : 30000, 
                            action : {
                                text : 'Got It!',
                                onClick : (e, toastObject) => {
                                    toastObject.goAway(0);
                                }
                            }
                        }); 
                    }
                    EventBus.$emit('rebateUpdate', rebate.data);
                })
                .listen('ChatMessageReceived', (received_org_message) => {
                    this.$emit('chatMessageReceived', received_org_message);
                });
                // bind sub success to subCb if present
                if(subCb && subCb.constructor == Function) {
                    channel.on('pusher:subscription_succeeded', subCb);
                }
            }
            connectStream();
            
        } else {
            //console.error("Missing Organisation UUID");
            let re = confirm("Failed to load Organisation Credentials\nPlease reload your page\n\nIf problem persists, please contact an administrator\nReload Now?");
            if(re) {
                location.reload();
            }
        }

        // Event listener for removing things from no cares
        EventBus.$on('addToNoCares', this.addToNoCares);
        EventBus.$on('removefromNoCares', this.removefromNoCares);

        // Event listener that listens for theme toggle event to keep track of theme state
        EventBus.$on('toggleTheme', this.setThemeState);
    }
});

// test code
// import emulation from './emulate';
// emulation.setApp(app);
// emulation.init();