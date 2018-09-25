<template>
    <div dusk="chat-bar" class="chat-bar" v-bind:class="{ 'active': opened }">
        <div class="chat-content">
            <div class="chat-bar-toggle" @click="fireChatBar()">
                <span class="icon icon-arrows-right"></span>
            </div>
        
            <div class="container-fluid organisation-chat">
                <div class="chat-header text-center pt-1 mb-3 pr-2">
                    <span class="icon icon-chat float-left"></span>
                    <h3 class="pr-3">Messages</h3>
                    <h3 id="chat-bar-dismiss" class="float-right close" @click="fireChatBar()">x</h3>
                </div>
                <div class="chat-inner-wrapper pr-2">
                    <b-card ref="chat_history" class="chat-history">
                        <b-row  v-for="(message, index) in display_messages" 
                                :key="index"
                                v-bind:class="{
                                    'mt-4': index != 0, 
                                    'admin-chat': message.user_name == 'Market Martial',
                                    'own-chat': message.user_name == $root.config('user_preferences.user_name'), 
                                }">
                            <b-col :offset-md="message.user_name == $root.config('user_preferences.user_name')? 2 : 0" 
                                    cols="10" 
                                    class="chat-block">
                                <b-row>
                                    <b-col cols="12" class="chat-user-name mb-0 pt-3">
                                        <h6 class="m-0">{{ messageUserName(message.user_name) }}</h6>
                                    </b-col>
                                    <b-col cols="12" class="chat-message mt-3">
                                        <p>{{ message.message }}</p>
                                    </b-col>
                                    <b-col cols="12" class="chat-time mt-0">
                                        <h6 v-bind:class="{'float-right': message.user_name != $root.config('user_preferences.user_name'), 
                                        }">
                                            {{ castToMoment(message.time_stamp) }}
                                            <font-awesome-icon class="float-right" v-if="message.status == 'sent'" icon="check"></font-awesome-icon>
                                            <font-awesome-icon class="float-right" v-if="message.status == 'received'" icon="check-double"></font-awesome-icon>
                                        </h6>
                                            
                                    </b-col>
                                </b-row>
                            </b-col>    
                        </b-row>
                    </b-card>
                    
                    <div class="chat-actions">
                        <b-form @submit.stop.prevent="sendMessage" id="chat-message-form">
                            <b-form-group v-for="(option, index) in quick_message_options" :key="index" class="text-center mb-1">
                                <button @click="quick_message = option.message" type="submit" class="btn mm-generic-trade-button w-100">{{ option.title }}</button>
                            </b-form-group>
                            <textarea @keydown.enter.prevent="sendMessage"
                                      rows="6"
                                      class="mb-1 mt-1 w-100"
                                      v-model="new_message"
                                      placeholder="Enter your message here...">
                            </textarea>  
                            
                            <b-form-group class="text-center mb-0 pb-0">
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
                new_message: "",
                quick_message: "",
                display_messages: [],
                quick_message_options: [
                    {
                        title: 'No cares, thanks',
                        message: 'No cares, thanks',
                    },
                    {
                        title: 'Looking',
                        message: 'Looking',
                    },
                    {
                        title: 'Please call me',
                        message: 'Please call me',
                    },
                ],
            };
        },
        methods: {
            doSomething(e) {
                console.log("WORKING");
            },
            sendMessage() {
                if(this.new_message || this.quick_message) {
                    let sendMessage = this.new_message ? {new_message: this.new_message} : {quick_message:this.quick_message};
                    axios.post(axios.defaults.baseUrl + "/trade/organisation-chat", sendMessage)
                    .then(response => {
                        let chat_history = this.$refs.chat_history;
                        this.new_message = "";
                        this.quick_message = "";
                        this.display_messages.push(response.data.data);
                        this.display_messages[this.display_messages.length -1].status = "sent";
                        
                        Vue.nextTick( () => {
                            chat_history.scrollTop = chat_history.scrollHeight;
                        });
                    })
                    .catch(err => {
                        console.log("TEST ERRORS ", err.response.data);
                        reject(new Errors(err.response.data));
                    });
                } else {
                    this.new_message = "";
                    this.quick_message = "";
                }

            },
            toggleBar(set) {
                if(typeof set != 'undefined') {
                    this.opened = set == true;
                } else {
                    this.opened = !this.opened;
                }
                let chat_history = this.$refs.chat_history;
                chat_history.scrollTop = chat_history.scrollHeight;
                this.$root.message_count = this.opened ? 0 : this.$root.message_count;
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
            /**
             * Listens for a chatMessageReceived event firing
             *
             * @event /$root#chatMessageReceived
             */
            newChatMessageListener() {
                this.$root.$on('chatMessageReceived', this.addNewMessage);
            },
            castToMoment(date_string) {
                return moment(date_string, "X").format('H:mmA, DD MMM YYYY');
            },
            loadChatHistory() {
                axios.get(axios.defaults.baseUrl + '/trade/organisation-chat')
                .then(chatHistoryResponse => {
                    if(chatHistoryResponse.status == 200) {
                        this.display_messages = chatHistoryResponse.data.data;
                        Vue.nextTick( () => {
                            this.$refs.chat_history.scrollTop = this.$refs.chat_history.scrollHeight;
                        });
                    } else {
                        console.error(err);    
                    }
                }, err => {
                    console.error(err);
                });
            },
            addNewMessage(message) {
                let message_index;
                let chat_history = this.$refs.chat_history;
                let should_scroll = false;
                if(chat_history.scrollTop === (chat_history.scrollHeight - chat_history.offsetHeight) ) {
                    should_scroll = true;
                }
                if (this.display_messages.length > 0) {
                    message_index = this.display_messages.findIndex( (listed_message) => {
                        return listed_message.user_name == message.user_name
                            && listed_message.message == message.message 
                            && listed_message.time_stamp == message.time_stamp;
                    });
                }

                if(message_index == -1) {
                    this.display_messages.push(message);
                    this.$root.message_count = this.opened ? 0 : this.$root.message_count + 1;
                    if( this.display_messages[this.display_messages.length -1].user_name == this.$root.config('user_preferences.user_name') ) {
                        this.display_messages[this.display_messages.length -1].status = "received";
                    }
                    Vue.nextTick( () => {
                        chat_history.scrollTop = should_scroll ? chat_history.scrollHeight : chat_history.scrollTop;
                    });
                } else {
                    this.display_messages[message_index].status = 'received';
                }
            },
            messageUserName(username) {
                return username == this.$root.config('user_preferences.user_name')? "You": username;
            },
        },
        mounted() {
            this.chatBarListener();
            this.newChatMessageListener();
            this.loadChatHistory();
        }
    }
</script>