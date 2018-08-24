<template>
    <div v-if="loading" dusk="mm-loader" class="mm-loader w-100">
         <b-img :width="width" :height="height" center :src="resolveTheme" alt="loader" />
    </div>
</template>

<script>
    //lib imports
    import { EventBus } from '../lib/EventBus.js';
    export default {
        props:{
            'event_name': {
                type: String
            },
            'width': {
                type: String,
                default: '200'
            },
            'height': {
                type: String,
                default: '200'
            },
            'default_state': {
                type: Boolean
            },
            'theme': {
                type: String,
            }
        },
        data() {
            return {
                light_theme_img:'/img/loader_black.gif',
                dark_theme_img:'/img/loader_white.gif',
                loading: false
            };
        },
        computed: {
            resolveTheme: function () {
                switch (this.theme) {
                    case "dark":
                        return this.dark_theme_img;
                    case "light":
                        return this.light_theme_img;
                    default:
                        return this.dark_theme_img;
                }
            }
        },
        methods: {
            toggleLoader(set) {
                if(typeof set != 'undefined') {
                    this.loading = set == true;
                } else {
                    this.loading = !this.loading;
                }
            },
            /**
             * Fires the Loader toggle event
             *
             * @fires /lib/EventBus#toggleSidebar
             */
            /*fireLoadeder() {
                EventBus.$emit('loading', 'page');
            },*/
            
            /**
             * Listens for a pageLoaded event firing
             *
             * @event /lib/EventBus#pageLoaded
             */
            loaderListener() {
                EventBus.$on(this.event_name, this.toggleLoader);
            },
        },
        mounted() {
            this.loading = this.default_state;
            this.light_theme_img = axios.defaults.baseUrl + this.light_theme_img;
            this.dark_theme_img = axios.defaults.baseUrl + this.dark_theme_img;
            this.loaderListener();
        }
    }

</script>