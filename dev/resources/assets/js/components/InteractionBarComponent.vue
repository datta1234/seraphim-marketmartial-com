<template>
    <div class="interaction-bar" v-bind:class="{ 'active': opened }">
        <div class="interaction-content" ref="barContent">
            <component v-bind:is="currentBarContent" v-bind="component_props"></component>
        </div>
        <div class="interaction-bar-toggle" @click="toggleBar()">
            <span class="icon icon-arrows-right" v-if="!opened"></span>
            <span class="icon icon-arrows-left" v-if="opened"></span>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../lib/EventBus.js';
    export default {
        data() {
            return {
                opened: false,
                component: null,
                component_props: {}
            };
        },
        computed: {
            currentBarContent() {
                return this.component
            }
        },
        methods: {
            toggleBar(set, options) {
                if(typeof set != 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }

                // only handle if opened
                if(this.opened) {
                    console.log(options);
                    this.component = options.component;
                    this.component_props = options.props;
                }
            }
        },
        mounted() {
            EventBus.$on('interactionToggle', this.toggleBar)
        }
    }
</script>