<template>
    <div class="interaction-bar" v-bind:class="{ 'active': opened }">
        <div class="interaction-content" ref="barContent">
            <ibar-negotiation-bar :market-request="market_request"></ibar-negotiation-bar>
        </div>
        <div class="interaction-bar-toggle" @click="toggleBar(false)">
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
                market_request: null,
            };
        },
        computed: {

        },
        methods: {
            toggleBar(set, marketRequest) {
                if(typeof set != 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }

                // only handle if opened
                if(this.opened) {
                    this.market_request = {};
                    this.market_request = marketRequest;
                } else {
                    this.market_request = {};
                    this.market_request = null;
                }
            }
        },
        mounted() {
            EventBus.$on('interactionToggle', this.toggleBar);
        }
    }
</script>