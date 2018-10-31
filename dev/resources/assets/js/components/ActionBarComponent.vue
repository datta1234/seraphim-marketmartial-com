<template>
    <div dusk="action-bar" class="action-bar">
        <div class="row mt-2 menu-actions">
            <div class="col-9">
                <request-market-menu></request-market-menu>
                <important-menu :notifications="market_notifications.important" :markets="markets" :no_cares="no_cares"></important-menu>
                <alerts-menu :notifications="market_notifications.alert" :markets="markets" v-if="market_notifications.alert.length >0"></alerts-menu>
                
                <confirmations-menu :trade_confirmations="trade_confirmations" v-if="trade_confirmations.length >0"></confirmations-menu>
            </div>
            <div class="col-3">
                <div class="float-right">
                    <filter-markets-menu :markets="markets"></filter-markets-menu>
                    <button id="action-bar-open-chat" type="button" class="btn mm-transparent-button mr-2" @click="loadChatBar()" v-if="!chat_opened">
                        <span class="badge badge-danger message-alert-count" v-if="$root.message_count > 0">{{ $root.message_count }}</span>
                        <span class="icon icon-chat"></span> Chat
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    //component imports
    import FilterMarketsMenu from './ActionBar/Components/FilterMarketsMenuComponent.vue';
    import ImportantMenu from './ActionBar/Components/ImportantMenuComponent.vue';
    import AlertsMenu from './ActionBar/Components/AlertsMenuComponent.vue';
    import ConfirmationsMenu from './ActionBar/Components/ConfirmationsMenuComponent.vue';
    import RequestMarketMenu from './ActionBar/Components/RequestMarket/RequestMarketMenuComponent.vue';
   

    //lib imports
    import { EventBus } from '../lib/EventBus.js';
    import Market from '../lib/Market';
    import UserMarket from '../lib/UserMarket';
    import UserMarketRequest from '../lib/UserMarketRequest';
    import UserMarketNegotiation from '../lib/UserMarketNegotiation';
    import TradeConfirmation from '../lib/TradeConfirmation'

    export default {
        components: {
            FilterMarketsMenu,
            ImportantMenu,
            AlertsMenu,
            ConfirmationsMenu,
            RequestMarketMenu,

        },
        props:{
          'markets': {
            type: Array
          },
          'trade_confirmations': {
            type: Array
          },
          'no_cares': {
            type: Array
          }
        },
        watch: {
            'markets': {
                handler: function(){
                    this.reloadNotifications();
                },
                deep: true
            },
            'no_cares': {
                handler: function(){
                    this.reloadNotifications();
                },
                deep: true
            }
        },
        data() {
            return {
                market_notifications: {
                    important: [],
                    alert: [],
                    confirm: [],
                },
                modals: {
                    select_market: false
                },
                chat_opened: false,
            };
        },
        methods: {
            /**
             * Resets the quantities counters and updates each of the market_notifications
             *      counters according to the matching market requests
             */
            reloadNotifications() {
                this.market_notifications.important = [];
                this.market_notifications.alert = [];
                this.market_notifications.confirm = [];

                let important_states = ['REQUEST','REQUEST-VOL'];
                let alert_states = ['alert'];
                let confirm_states = ['confirm'];


                this.markets.forEach(market => {

                    market.market_requests.forEach(market_request => {

                        if(market_request.attributes.action_needed || alert_states.indexOf(market_request.attributes.state) > -1) //if this market request is in need of attention its considerd important
                        {
                            this.market_notifications.alert.push(market_request);

                        }else if(important_states.indexOf(market_request.attributes.state) > -1 && this.no_cares.indexOf(market_request.id) == -1) //if its important and hasnt been placed in no cares
                        {
                            if(!market_request.quotes.find(quote => quote.is_maker) && !market_request.is_interest)
                            {
                                this.market_notifications.important.push(market_request);
                            }
                        }
                    });
                });

                    // if(true)
                    //     {
                    //         this.market_notifications.confirm.push(market_request);
                    //     }

            },
            toggleBar(set) {
                if(typeof set != 'undefined') {
                    this.chat_opened = set == true;
                } else {
                    this.chat_opened = !this.chat_opened;
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
            this.reloadNotifications();
            this.chatBarListener();
        }
    }

</script>