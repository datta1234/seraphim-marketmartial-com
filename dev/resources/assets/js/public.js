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
    theme: 'primary'
});

Vue.use(FlatSurfaceShader);
Vue.component('header-canvas', require('./components/HeaderCanvas.vue'));

import ActiveMarketMakers from './components/ActiveMarketMakers.vue'
Vue.component('active-makers', ActiveMarketMakers);

const app = new Vue({
    el: '#app',
    methods: {
        
    },
    data: {
        
    },
    mounted: function() {
        ActiveMakerService.init(this);
    }
});