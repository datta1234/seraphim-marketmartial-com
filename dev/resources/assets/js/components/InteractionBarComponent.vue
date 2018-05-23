<template>
    <div class="interaction-bar" v-bind:class="{ 'active': opened }">
        <div class="interaction-content">
            BLAHHHHHH
        </div>
        <div class="interaction-bar-toggle" @click="">
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
                opened: false
            };
        },
        methods: {
            toggleBar(p1, p2) {
                console.log('toggled', p1, p2);
                this.opened = !this.opened;
            }
        },
        mounted() {
            EventBus.$on('interactionToggle', this.toggleBar)
        }
    }
</script>