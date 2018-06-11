<template>
    <div dusk="chat-bar" class="chat-bar" v-bind:class="{ 'active': opened }">
        <div class="chat-bar-toggle" @click="loadChatBar()">
            <span class="icon icon-arrows-right"></span>
        </div>
        <div class="chat-content">
        
            <div class="container-fluid">
                <div class="chat-header row pt-1">
                    <div class="col-12 text-center">
                        <span class="icon icon-chat float-left"></span>
                        <h3>Messages</h3>
                        <h3 id="chat-bar-dismiss" class="float-right close" @click="loadChatBar()">x</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="chats col-12 h-100">
                        
                    </div>
                </div>
                <div class="chat-action-wrapper row mt-1 mb-3">
                    <div class="col-12">
                        <form action="" method="POST">
                            <div class="form-group mb-2">
                                <textarea class="form-control" name="message" value="" rows="6" placeholder="Enter your message here..."></textarea>
                            </div>
                            <div class="form-group mb-2">
                                <button type="submit" class="btn mm-generic-trade-button">Send message</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 mt-1">
                        <button type="button" class="btn mm-generic-trade-button float-right w-50">No cares, thanks</button>
                    </div>
                    <div class="col-12 mt-1">
                        <button type="button" class="btn mm-generic-trade-button float-right w-50">Looking</button>
                    </div>
                    <div class="col-12 mt-1">
                        <button type="button" class="btn mm-generic-trade-button float-right w-50">Please call me</button>
                    </div>
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
            toggleBar(set) {
                if(typeof set != 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }
            },
            /**
             * Loads the Chat Sidebar
             *
             * @fires /lib/EventBus#toggleSidebar
             */
            loadChatBar() {
                EventBus.$emit('toggleSidebar', 'chat');
            },
            /**
             * Listens for a chatToggle event firing
             *
             * @event /lib/EventBus#chatToggle
             */
            chatBarListener() {
                EventBus.$on('chatToggle', this.toggleBar);
            },
        },
        mounted() {
            this.chatBarListener();
        }
    }
</script>