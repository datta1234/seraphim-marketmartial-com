
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
    theme: 'primary'
})
import 'bootstrap-vue/dist/bootstrap-vue.css';
import { Bar } from 'vue-chartjs';

/**
 * Load the Fontawesome vue component then the library and lastly import
 * and register the icons you want to use.
 */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faSearch } from '@fortawesome/free-solid-svg-icons';

library.add(faSearch);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// datepicker
Vue.component('Datepicker', Datepicker);

// Font Awesome component
Vue.component('font-awesome-icon', FontAwesomeIcon);

// Profile Components
Vue.component('email-settings', require('./components/Profile/Components/EmailSettingsComponent.vue'));
Vue.component('activate-input', require('./components/Profile/Components/ActivateInputComponent.vue'));
Vue.component('toggle-input', require('./components/Profile/Components/ToggleInputComponent.vue'));
Vue.component('day-month-picker', require('./components/Profile/Components/DayMonthPickerComponent.vue'));

// Admin Components
Vue.component('users-table', require('./components/Admin/Users/UsersTableComponent.vue'));
//Vue.component('create-user', require('./components/Admin/Users/CreateUserComponent.vue'));

// Stats Components
Vue.component('monthly-activity', require('./components/Stats/MonthlyActivityComponent.vue'));

const app = new Vue({
    el: '#trade_app',
    methods: {
        
    },
    data: {
        
    },
    mounted: function() {
        
    }
});
