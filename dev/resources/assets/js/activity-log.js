
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./components/data-methods');

window.Vue = require('vue');
window.moment = require('moment');

// datepicker
import Datepicker from 'vuejs-datepicker';
Vue.component('Datepicker', Datepicker);

// vue-bootstrap
import BootstrapVue from 'bootstrap-vue';
import 'bootstrap-vue/dist/bootstrap-vue.css';

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
});

Vue.component('activity-log-modal', require('./components/ActivityLogModal.vue'));

const activity_log_app = new Vue({
    el: '#activity_log_app',
    data: {
        activity_types: {}
    },
    mounted() {
        this.activity_types = window.activity_types;
    }
});
