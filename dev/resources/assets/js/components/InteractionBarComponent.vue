<template>
    <div class="interaction-bar" v-bind:class="{ 'active': opened }">
        <div class="interaction-content">

            <!-- sub component -->
            <div class="container-fluid">
                <div class="row">
                    {{ subject }}
                </div>
            </div>

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
                subject: null
            };
        },
        methods: {
            toggleBar(set, subject) {
                this.subject = subject;
                if(typeof set != 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }
            }
        },
        mounted() {
            EventBus.$on('interactionToggle', this.toggleBar)
        }
    }
</script>