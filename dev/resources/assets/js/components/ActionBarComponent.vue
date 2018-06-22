<template>
    <div dusk="action-bar" class="action-bar">
        <div class="row mt-2 menu-actions">
            <div class="col-9">
                <request-market-menu></request-market-menu>
                <important-menu :count="market_quantities.important" :markets="markets" :no_cares="no_cares"></important-menu>
                <alerts-menu :count="market_quantities.alert" :markets="markets" v-if="market_quantities.alert>0"></alerts-menu>
                <confirmations-menu :count="market_quantities.confirm" :markets="markets" v-if="market_quantities.confirm>0"></confirmations-menu>
            </div>
            <div class="col-3">
                <div class="float-right">
                    <filter-markets-menu :markets="markets"></filter-markets-menu>
                    <button id="action-bar-open-chat" type="button" class="btn mm-transparent-button mr-2" @click="loadChatBar()" v-if="!chat_opened">
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

    export default {
        components: {
            FilterMarketsMenu,
            ImportantMenu,
            AlertsMenu,
            ConfirmationsMenu,
            RequestMarketMenu
        },
        props:{
          'markets': {
            type: Array
          },
          'no_cares': {
            type: Array
          }
        },
        watch: {
            'markets': {
                handler: function(){
                    console.log('change list');
                    this.reloadQuantities();
                },
                deep: true
            },
            'no_cares': {
                handler: function(){
                    console.log('added no care', this.no_cares);
                },
                deep: true
            }
        },
        data() {
            return {
                market_quantities: {
                    important: 1,
                    alert: 1,
                    confirm: 1,
                },
                modals: {
                    select_market: false
                },
                chat_opened: false,
            };
        },
        methods: {
            /**
             * Resets the quantities counters and updates each of the market_quantities
             *      counters according to the matching market requests
             */
            reloadQuantities() {
                this.market_quantities.important = 0;
                this.market_quantities.alert = 0;
                this.market_quantities.confirm = 0;
                this.markets.forEach(market => {
                    market.market_requests.forEach(request => {
                        switch(request.attributes.state) {    
                            case "vol-spread-alert":
                            case "alert":
                                this.market_quantities.alert++;
                            break;
                            case "confirm":
                                this.market_quantities.confirm++;
                            break;
                            case "request":
                            case "request-grey":
                            case "sent":
                            case "vol-spread":
                            default:
                                this.market_quantities.important++;
                        }
                    });
                });
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
           this.reloadQuantities();
           this.chatBarListener();
        }
    }

</script>