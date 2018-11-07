<template>
    <div dusk="theme-toggle" class="theme-toggle">
         <!-- Rounded toggle switch -->
        <div class="float-right">
            <span class="toggle">Theme toggle</span>
            <label class="switch mb-0 ml-1" id="theme-toggle">
                <input type="checkbox" v-model="theme_toggler">
                <span class="slider round"></span>
            </label>
        </div>
    </div>
</template>

<script>
    //lib imports
    import { EventBus } from '~/lib/EventBus.js';
    export default {
        data() {
            return {
                theme_toggler: false
            };
        },
        watch: {
            'theme_toggler': function(val) {
                EventBus.$emit('theme', val);
            }
        },
        methods: {
        },
        mounted() {
            if (localStorage.getItem('themeState') != null) {
                try {
                    this.theme_toggler = localStorage.getItem('themeState') === 'true';
                } catch(e) {
                    this.theme_toggler = false;
                    localStorage.removeItem('themeState');
                }
            } else {
                this.theme_toggler = false;
                try {
                    localStorage.setItem('themeState', this.theme_toggler);
                } catch(e) {
                    localStorage.removeItem('themeState');
                }
            }
        }
    }

</script>