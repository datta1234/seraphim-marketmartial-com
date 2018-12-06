
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.moment = require('moment');

// Bootstrap Vue
import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);

// Format Mixin - rand/qty formatting
import FormatMixin from './FormatMixin.js'
Vue.mixin(FormatMixin);

// components
import TradedMarkets from './components/PreviousDay/TradedMarkets.vue';
import UntradedMarkets from './components/PreviousDay/UntradedMarkets.vue';

// globals
Vue.component('market-group', require('./components/MarketGroupComponent.vue'));
Vue.component('market-tab', require('./components/PreviousDay/MarketTab.vue'));

// Models
import Market from './lib/Market';
import UserMarketRequest from './lib/UserMarketRequest';
import Config from './lib/Config';

// Vue App
const app = new Vue({
    el: '#previous_day_app',
    components: {
        TradedMarkets,
        UntradedMarkets
    },
    data() {
        return {
            configs: {},
            display_markets_traded: [],
            display_markets_untraded: []
        }
    },
    methods: {
        loadMarkets() {
            return axios.get(axios.defaults.baseUrl + '/previous-day/markets')
            .then(response => {
                this.display_markets_traded = response.data.map(x => new Market(x));
                this.display_markets_untraded = response.data.map(x => new Market(x));
                return response;
            });
        },
        loadMarketRequests() {
            return axios.get(axios.defaults.baseUrl + '/previous-day/market-requests')
            .then(response => {
                console.log(response);
                this.display_markets_traded.forEach(market => {
                    market.addMarketRequests(response.data.traded_market_requests.filter(x => x.market_id == market.id));
                });

                this.display_markets_untraded.forEach(market => {
                    market.addMarketRequests(response.data.untraded_market_requests.filter(x => x.market_id == market.id));
                });
                return response;
            });
        },
        loadConfigs(config_list) {
            let promises = [];
            config_list.forEach(config => {
                // console.log(config)
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
    },
    mounted() {
        Config.configs = this.configs;
        // load config files
        this.loadConfigs([
            "trade_structure",
            "app",
        ])
        .catch(err => {
            console.error(err);
            // @TODO: handle this with critical failure... no config = no working trade screen
        })
        .then(this.loadMarkets)
        .then(this.loadMarketRequests)
        .then(() => {
            console.log("Loaded Data: ", this.display_markets);
        })
        .catch(err => {
            console.error(err);
        });
    }
});