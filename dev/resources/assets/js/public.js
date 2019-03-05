require('./bootstrap');
require('./components/data-methods');

import Vue from 'vue';
import FlatSurfaceShader from 'vue-flat-surface-shader';

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Toasted from 'vue-toasted';
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
});

Vue.use(FlatSurfaceShader);
Vue.component('header-canvas', require('./components/HeaderCanvas.vue'));

import ActiveMakerService from '~/services/ActiveMakerService';
import ActiveMarketMakers from './components/ActiveMarketMakers.vue'
Vue.component('active-makers', ActiveMarketMakers);

const app = new Vue({
    el: '#app',
    methods: {
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
            return window.axios.get(window.axios.defaults.baseUrl + '/config/'+config_file)
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
        config(path) {
            return path.split('.').reduce((acc, cur) => {
                if(acc && typeof acc[cur] !== 'undefined') {
                    return acc[cur];
                }
                return undefined;
            }, this.configs);
        },
    },
    data: {
        configs: {}
    },
    mounted: function() {
        this.loadConfigs([
            "app",
        ])
        .then(() => {
            ActiveMakerService.init(this);
        })
    }
});