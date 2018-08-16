<template>
    <div dusk="chat-bar" class="chat-bar" v-bind:class="{ 'active': opened }">
        <div class="chat-content">
            <div class="chat-bar-toggle" @click="fireChatBar()">
                <span class="icon icon-arrows-right"></span>
            </div>
        
            <div class="container-fluid">
                <div class="chat-header row pt-1">
                    <div class="col-12 text-center">
                        <span class="icon icon-chat float-left"></span>
                        <h3>Messages</h3>
                        <h3 id="chat-bar-dismiss" class="float-right close" @click="fireChatBar()">x</h3>
                    </div>
                </div>

                <div class="chat-action-wrapper row mt-1 mb-3">
                    <b-row>
                        <b-col cols="12" class="chat-history h-100">
                            <b-row v-for="(message, index) in display_messages" class="mt-2">
                                <b-col cols="6" class="chat-time">
                                    {{message.time_stamp}}
                                </b-col>
                                <b-col cols="6" class="chat-user-name">
                                    {{message.user_name}}
                                </b-col>
                                <b-col cols="12" class="chat-message">
                                    <p>{{message.message}}</p>
                                </b-col>
                            </b-row> 
                        </b-col>
                    </b-row>
                    
                    <div class="chat-actions row mt-1 mb-3">
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
                                                 v-model="new_message"
                                                 placeholder="Enter your message here..."
                                                 :rows="6"
                                                 :max-rows="6"
                                                 :no-resize="true">
                                </b-form-textarea>
                                
                                <b-form-group class="text-center mb-2 pb-2">
                                    <button type="submit" class="btn mm-generic-trade-button w-100">Send message</button>
                                </b-form-group>
                            </b-form>
                        </div>
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
                new_message: "",
                display_messages: []
            };
        },
        methods: {
            sendMessage(evt) {
                evt.preventDefault();
                if(this.new_message) {
                    axios.post(axios.defaults.baseUrl + "/trade/organisation-chat", {message: this.new_message})
                    .then(response => {
                        // TODO add message to list with sending icon
                        // NOTE when we get response from pusher we will check against message and change icon to sent(check) icon
                        // response.data.data.message
                        // response.data.data.time_stamp
                        // response.data.data.user_name
                        this.new_message = "";
                        this.display_messages.push(response.data.data);
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