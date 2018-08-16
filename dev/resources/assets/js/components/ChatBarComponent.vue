<template>
    <div dusk="chat-bar" class="chat-bar" v-bind:class="{ 'active': opened }">
        <div class="chat-bar-toggle" @click="fireChatBar()">
            <span class="icon icon-arrows-right"></span>
        </div>
        <div class="chat-content">
        
            <div class="container-fluid">
                <div class="chat-header row pt-1">
                    <div class="col-12 text-center">
                        <span class="icon icon-chat float-left"></span>
                        <h3>Messages</h3>
                        <h3 id="chat-bar-dismiss" class="float-right close" @click="fireChatBar()">x</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="chats col-12 h-100">
                        
                    </div>
                </div>
                <div class="chat-action-wrapper row mt-1 mb-3">
                    <div class="text-center col-12 mt-1">
                        <button type="button" class="btn mm-generic-trade-button w-100">No cares, thanks</button>
                    </div>
                    <div class="text-center col-12 mt-1">
                        <button type="button" class="btn mm-generic-trade-button w-100">Looking</button>
                    </div>
                    <div class="text-center col-12 mt-1">
                        <button type="button" class="btn mm-generic-trade-button w-100">Please call me</button>
                    </div>
                    <div class="col-12 mt-2">
                        <b-form @submit="sendMessage" id="chat-message-form">
                            <b-form-textarea class="mb-2"
                                             v-model="message"
                                             placeholder="Enter your message here..."
                                             :rows="6"
                                             :max-rows="6"
                                             :no-resize="true">
                            </b-form-textarea>
                            
                            <b-form-group class="text-center mb-2">
                                <button type="submit" class="btn mm-generic-trade-button w-100">Send message</button>
                            </b-form-group>
                        </b-form>
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
                message: "",
            };
        },
        methods: {
            sendMessage(evt) {
                evt.preventDefault();
                if(this.message) {
                    axios.post(axios.defaults.baseUrl + "/trade/organisation-chat", {message: this.message})
                    .then(response => {
                        // TODO add message to list with sending icon
                        // NOTE when we get response from pusher we will check against message and change icon to sent(check) icon
                        // response.data.data.message
                        // response.data.data.time_stamp
                        // response.data.data.user_name
                        console.log("SUCCESS: ",response);
                    })
                    .catch(err => {
                        reject(new Errors(err.response.data));
                    });
                } else {
                    console.log("I AM EMPTY!");
                    // handle empty message field
                }
            },
            toggleBar(set) {
                if(typeof set != 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }
            },
            /**
             * Fires the Chat Bar toggle event
             *
             * @fires /lib/EventBus#toggleSidebar
             */
            fireChatBar() {
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