<template>
    <div dusk="chat-bar" class="chat-bar" v-bind:class="{ 'active': opened }">
        <div class="chat-content">
            <div class="chat-bar-toggle" @click="fireChatBar()">
                <span class="icon icon-arrows-right"></span>
            </div>
        
            <div class="container-fluid organisation-chat">
                <div class="chat-header text-center pt-1 mb-3 pr-2">
                    <span v-bind:class="{'new-message-alert': new_incoming}" class="icon icon-chat float-left"></span>
                    <h3 class="pr-3">Messages</h3>
                    <h3 id="chat-bar-dismiss" class="float-right close" @click="fireChatBar()">x</h3>
                </div>
                <div class="chat-inner-wrapper pr-2">
                    <b-card ref="chat_history" class="chat-history">
                        <b-row fluid v-for="(display_messages, key) in display_messages_grouped" :key="key">
                            <b-col>
                                <b-row class="timeblock-chat">
                                    <b-col>{{ key }}</b-col>
                                </b-row>
                                <b-row  v-for="(message, index) in display_messages" 
                                        :key="index"
                                        v-bind:class="{
                                            'chat-row': true,
                                            'mt-4': index != 0, 
                                            'admin-chat': message.user_name == 'Market Martial',
                                            'own-chat': message.user_name == $root.config('user_preferences.user_name'),
                                        }">
                                    <b-col :offset-md="message.user_name == $root.config('user_preferences.user_name') ? 2 : 0" 
                                            cols="10" 
                                            class="chat-block">
                                        <b-row>
                                            <div class="w-100 chat-user-name pt-2 pr-2 pl-2">
                                                <h6 class="m-0">
                                                    {{ messageUserName(message.user_name) }}
                                                    <span class="float-right chat-timestamp">{{ castToMomentTime(message.time_stamp) }}</span>
                                                </h6>
                                            </div>
                                            <div class="w-100 chat-message mt-1 pr-2 pl-2">
                                                <p class="mb-0">{{ message.message }}</p>
                                            </div>
                                            <div class="w-100 chat-time mt-0 pr-2 pl-2 pb-2">
                                                <h6 class="text-right mb-0" v-if="message.status != null">
                                                    &nbsp;
                                                    <i v-if="message.status == 'sent'" class="fas fa-check"></i>
                                                    <i v-if="message.status == 'received'" class="fas fa-check-double"></i>
                                                </h6>
                                            </div>
                                        </b-row>
                                    </b-col>    
                                </b-row>
                            </b-col>
                        </b-row>
                    </b-card>
                    
                    <div class="chat-actions">
                        <b-form @submit.stop.prevent="sendMessage" id="chat-message-form">
                            <template v-for="(option, index) in quick_messages">
                                <button v-active-request 
                                        @click="quick_message = option.id" 
                                        type="submit" 
                                        class="btn mm-generic-trade-button w-50 mb-1">
                                    {{ option.title }}
                                </button>
                            </template>
                            <textarea v-if="chat_limited"
                                      @keydown.enter.prevent="sendMessage"
                                      rows="6"
                                      class="mb-1 mt-1 w-100"
                                      v-model="new_message"
                                      placeholder="Enter your message here...">
                            </textarea>  
                            
                            <b-form-group v-if="chat_limited" class="text-center mb-0 pb-0">
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
    import { EventBus } from '~/lib/EventBus.js';
    export default {
        props: [
            'chat_limited',
            'quick_messages'
        ],
        data() {
            return {
                new_incoming: false,
                opened: false,
                new_message: "",
                quick_message: "",
                display_messages: [],
            };
        },
        methods: {
            sendMessage() {
                if(this.new_message || this.quick_message !== null) {
                    let sendMessage = this.quick_message ? 
                        {quick_message:this.quick_message}
                        : {new_message: this.new_message};
                    axios.post(axios.defaults.baseUrl + "/trade/organisation-chat" , sendMessage)
                    .then(response => {
                        let chat_history = this.$refs.chat_history;
                        this.new_message = "";
                        this.quick_message = null;
                        this.display_messages.push(response.data.data);
                        this.display_messages[this.display_messages.length -1].status = "sent";
                        
                        Vue.nextTick( () => {
                            chat_history.scrollTop = chat_history.scrollHeight;
                        });
                    }, err => {
                        this.quick_message = null;
                        //console.error(err);
                        this.$toasted.error(err.response.data.message);
                    });
                } else {
                    this.new_message = "";
                    this.quick_message = null;
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
            toggleNewIncomingState(set){
                if(typeof set != 'undefined') {
                    this.new_incoming = set == true;
                } else {
                    this.new_incoming = !this.new_incoming;
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
            /**
             * Listens for a chatMessageReceived event firing
             *
             * @event /$root#chatMessageReceived
             */
            newChatMessageListener() {
                this.$root.$on('chatMessageReceived', this.addNewMessage);
            },
            humanizeDate(date_string) {
                return moment(date_string, "X").calendar(null, {
                    sameDay: '[Today]',
                    lastDay: '[Yesterday]',
                    sameElse: 'DD MMM YYYY',
                });
            },
            castToMomentDate(date_string) {
                return moment(date_string, "X").format('DD MMM YYYY');
            },
            castToMomentTime(date_string) {
                return moment(date_string, "X").format('H:mmA');
            },
            loadChatHistory() {
                axios.get(axios.defaults.baseUrl + '/trade/organisation-chat')
                .then(chatHistoryResponse => {
                    this.display_messages = chatHistoryResponse.data.data;
                    Vue.nextTick( () => {
                        this.$refs.chat_history.scrollTop = this.$refs.chat_history.scrollHeight;
                    });
                }, err => {
                    //console.error(err);
                    this.$toasted.error(err.response.data.message);
                });
            },
            addNewMessage(message) {
                let chat_history = this.$refs.chat_history;
                let should_scroll = false;
                if(chat_history.scrollTop === (chat_history.scrollHeight - chat_history.offsetHeight) ) {
                    should_scroll = true;
                }

                let message_index = this.display_messages.findIndex( (listed_message) => {
                    return listed_message.user_name == message.user_name
                        && listed_message.message == message.message 
                        && listed_message.time_stamp == message.time_stamp;
                });

                if(message_index == -1) {
                    this.display_messages.push(message);
                    this.$root.message_count = this.opened ? 0 : this.$root.message_count + 1;
                    if( this.display_messages[this.display_messages.length -1].user_name == this.$root.config('user_preferences.user_name') ) {
                        this.display_messages[this.display_messages.length -1].status = "received";
                    }
                    Vue.nextTick( () => {
                        if(should_scroll) {
                            chat_history.scrollTop = chat_history.scrollHeight;
                        } else {
                            this.toggleNewIncomingState(true);
                        }
                    });

                    // If message is set to open chat open if not already open
                    if(message.auto_open_chat && !this.opened) {
                        this.fireChatBar();
                    }

                } else {
                    this.display_messages[message_index].status = 'received';
                }
            },
            messageUserName(username) {
                return username == this.$root.config('user_preferences.user_name')? "You": username;
            },
            resetNewMessageState() {
                if(this.new_incoming && this.$refs.chat_history.scrollTop === 
                    (this.$refs.chat_history.scrollHeight - this.$refs.chat_history.offsetHeight) ) {
                    this.toggleNewIncomingState(false);
                }
            },
        },
        computed: {
            display_messages_grouped: function() {
                return this.display_messages.reduce((acc, message) => {
                    let date = this.humanizeDate(message.time_stamp);
                    // define date index if not exist
                    if(typeof acc[date] === 'undefined') {
                        acc[date] = [];
                    }
                    // push to date index
                    acc[date].push(message);
                    return acc;
                }, {});
            }
        },
        mounted() {
            this.$refs.chat_history.onscroll = this.resetNewMessageState;
            this.chatBarListener();
            this.newChatMessageListener();
            this.loadChatHistory();
        }
    }
</script>