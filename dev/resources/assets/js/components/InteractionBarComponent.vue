<template>
    <div dusk="interaction-bar" class="interaction-bar" v-bind:class="{ 'active': opened }">
        <div class="interaction-content" ref="barContent">
            <ibar-negotiation-bar :market-request="market_request"></ibar-negotiation-bar>
        </div>
        <div class="interaction-bar-toggle" @click="toggleBar(false)">
            <span class="icon icon-arrows-left"></span>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../lib/EventBus.js';
    import NegotiationBar from './InteractionBar/NegotiationBar.vue';

    export default {
        components: {
            'ibar-negotiation-bar': NegotiationBar,
        },
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

                // only populate if opened
                if(this.opened) {
                    this.market_request = {};
                    this.market_request = marketRequest;
                    EventBus.$emit('interactionChange', this.market_request);
                } else {
                    this.market_request = {};
                    this.market_request = null;
                    // fire close event if its closing
                    if(this.opened == false) {
                        EventBus.$emit('interactionClose');
                    }
                }
            }
        },
        mounted() {
            EventBus.$on('interactionToggle', this.toggleBar);
        }
    }
</script>