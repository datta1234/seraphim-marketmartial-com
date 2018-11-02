
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
Vue.component('booked-trades-table', require('./components/Admin/BookedTrades/BookedTradesTableComponent.vue'));
Vue.component('rebates-table', require('./components/Admin/Rebates/RebatesTableComponent.vue'));
Vue.component('download-csv', require('./components/Admin/downloadCsvComponent.vue'));
Vue.component('bank-activity', require('./components/Admin/Stats/BankActivityComponent.vue'));
Vue.component('rebates-assigned', require('./components/Admin/Rebates/RebatesAssignedComponent.vue'));

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

Vue.mixin({
    methods: {
        /*
         * Basic bubble sort that sorts a date string array usesing Moment.
         *
         * @param {String[]} date_string_array - array of date string
         * @param {String} format - the format to cast to a moment object
         */
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
            let sign = '';
            //Check if our passed value is negative signed
            if( ("" + val).indexOf('-') !== -1 ) 
            {
                sign = tempVal.slice(0,tempVal.indexOf('-') + 1);
                tempVal = tempVal.slice(tempVal.indexOf('-') + 1);
            }
            //Check if our passed value is a float
            if( ("" + tempVal).indexOf('.') !== -1 ) 
            {
                floatVal = tempVal.slice(tempVal.indexOf('.'));
                tempVal = tempVal.slice(0,tempVal.indexOf('.'));
            }
            //Creates an array of chars reverses and itterates through it
            return sign + tempVal.split('').reverse().reduce(function(x,y) {
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
    }
});

import ActiveMarketMakers from './components/ActiveMarketMakers.vue'
Vue.component('active-makers', ActiveMarketMakers);
const app = new Vue({
    el: '#trade_app',
    methods: {
        
    },
    data: {
        
    },
    mounted: function() {
        
    }
});
