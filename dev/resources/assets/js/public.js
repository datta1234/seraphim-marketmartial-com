require('./bootstrap');
require('./components/data-methods');

import Vue from 'vue';
import FlatSurfaceShader from 'vue-flat-surface-shader';

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);
import 'bootstrap-vue/dist/bootstrap-vue.css';

Vue.use(FlatSurfaceShader);
Vue.component('header-canvas', require('./components/HeaderCanvas.vue'));

const app = new Vue({
    el: '#app',
    methods: {
        
    },
    data: {
        
    },
    mounted: function() {

    }
});