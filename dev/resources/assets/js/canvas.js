
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
import BootstrapVue from 'bootstrap-vue';
import Toasted from 'vue-toasted';
Vue.use(BootstrapVue);
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
import 'bootstrap-vue/dist/bootstrap-vue.css';
import { Bar } from 'vue-chartjs';

// Directives
import ActiveMakerService from '~/services/ActiveMakerService';
import ActiveMarketMakers from './components/ActiveMarketMakers.vue'
Vue.component('active-makers', ActiveMarketMakers);
import ActiveRequestDirective from './directives/active-request.js';
Vue.directive('active-request', ActiveRequestDirective);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// datepicker
Vue.component('Datepicker', Datepicker);

// Profile Components
Vue.component('email-settings', require('./components/Profile/Components/EmailSettingsComponent.vue'));
Vue.component('activate-input', require('./components/Profile/Components/ActivateInputComponent.vue'));
Vue.component('toggle-input', require('./components/Profile/Components/ToggleInputComponent.vue'));
Vue.component('day-month-picker', require('./components/Profile/Components/DayMonthPickerComponent.vue'));
Vue.component('terms-and-conditions', require('./components/Profile/Components/TermsAndConditionsComponent.vue'));

// Admin Components
Vue.component('users-table', require('./components/Admin/Users/UsersTableComponent.vue'));
//Vue.component('create-user', require('./components/Admin/Users/CreateUserComponent.vue'));
Vue.component('booked-trades-table', require('./components/Admin/BookedTrades/BookedTradesTableComponent.vue'));
Vue.component('rebates-table', require('./components/Admin/Rebates/RebatesTableComponent.vue'));
Vue.component('download-csv', require('./components/Admin/downloadCsvComponent.vue'));
Vue.component('bank-activity', require('./components/Admin/Stats/BankActivityComponent.vue'));
Vue.component('rebates-assigned', require('./components/Admin/Rebates/RebatesAssignedComponent.vue'));
Vue.component('brokerage-fee', require('./components/Admin/BrokerageFees/BrokerageFeesComponent.vue'));

// Stats Components
Vue.component('monthly-activity', require('./components/Stats/MonthlyActivityComponent.vue'));
Vue.component('activity-year-tables', require('./components/Stats/Components/ActivityYearTables.vue'));
Vue.component('all-market-activity', require('./components/Stats/AllMarketActivity.vue'));
Vue.component('safex-table', require('./components/Stats/Components/SafexTable.vue'));
Vue.component('upload-csv', require('./components/Stats/UploadCsvComponent.vue'));
Vue.component('open-interests', require('./components/Stats/OpenInterestsComponent.vue'));

// Rebate Componenets
Vue.component('rebates-earned', require('./components/Rebates/RebatesEarnedComponent.vue'));
Vue.component('rebates-year-tables', require('./components/Rebates/RebatesYearTablesComponent.vue'));

import FormatMixin from './FormatMixin.js'
Vue.mixin(FormatMixin);

const app = new Vue({
    el: '#canvas_app',
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
        configs: {},
        is_viewer: false,
    },
    mounted: function() {
        this.loadConfigs([
            "app",
        ])
        .then(() => {
            ActiveRequestDirective.init(this);
            ActiveMakerService.init(this);
        });

        let viewer_type = document.head.querySelector('meta[name="viewer-type"]');
        // test if a viewer type user is viewing the page, then disable some send features across the frontend
        if(viewer_type.content) {
            this.is_viewer = true;
        }
    }
});
