
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

// Admin Components
Vue.component('users-table', require('./components/Admin/Users/UsersTableComponent.vue'));
//Vue.component('create-user', require('./components/Admin/Users/CreateUserComponent.vue'));

// Stats Components
Vue.component('monthly-activity', require('./components/Stats/MonthlyActivityComponent.vue'));
Vue.component('activity-year-tables', require('./components/Stats/ActivityYearTables.vue'));

Vue.mixin({
    methods: {
        dateStringArraySort(date_string_array, format, ) {
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
    methods: {
        
    },
    data: {
        
    },
    mounted: function() {
        
    }
});
