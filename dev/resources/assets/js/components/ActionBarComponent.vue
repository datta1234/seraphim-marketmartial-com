<template>
    <div class="action-bar">
        <div class="row mt-2 menu-actions">
            <div class="col-9">
                <button type="button" class="btn mm-request-button mr-2 p-1">Request a Market</button>
                <button type="button" class="btn mm-important-button mr-2 p-1">Important</button>
                <button type="button" class="btn mm-alert-button mr-2 p-1">Alerts</button>
                <button type="button" class="btn mm-confirmation-button mr-2 p-1">Confirmations</button>
            </div>
            <div class="col-3">
                <div class="float-right">
                    <button type="button" class="btn mm-transparent-button mr-2">
                        <span class="icon icon-add"></span> Markets
                    </button>
                    <button type="button" class="btn mm-transparent-button mr-2" @click="loadChatBar()">
                        <span class="icon icon-chat"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../lib/EventBus.js';
    export default {
        data() {
            return {
                opened: false,
            };
        },
        methods: {
            loadChatBar() {
                this.opened = (this.opened) ? false : true;
                /*if(this.opened = 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }*/
                console.log("load Chat", this.opened);
                EventBus.$emit('chatToggle', this.opened);
            }

        },
        mounted() {
            
        }
    }
</script>