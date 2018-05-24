<template>
    <div class="action-bar">
        <div class="row mt-2 menu-actions">
            <div class="col-9">
                <button type="button" class="btn mm-request-button mr-2 p-1" @click="modals.select_market=true">Request a Market</button>
                <button type="button" class="btn mm-important-button mr-2 p-1">Important <strong>{{ market_quantities.important }}</strong></button>
                <button type="button" class="btn mm-alert-button mr-2 p-1" v-if="market_quantities.alert>0">Alerts <strong>{{ market_quantities.alert }}</strong></button>
                <button type="button" class="btn mm-confirmation-button mr-2 p-1" v-if="market_quantities.confirm>0">Confirmations <strong>{{ market_quantities.confirm }}</strong></button>
            </div>
            <div class="col-3">
                <div class="float-right">
                    <button id="popoverButton-event" type="button" class="btn mm-transparent-button mr-2" @click="onOpen">
                        <span class="icon icon-add"></span> Markets
                    </button>
                    <!-- Add market popover -->
                    <b-popover placement="bottomleft" ref="popover" target="popoverButton-event">
                        <div class="row text-center">
                            <div class="col-12">
                                <div v-for="(market,key) in availableSelectedMarkets" class="row mt-1">
                                    <div class="col-6 text-center pt-2 pb-2">
                                        <h5 class="w-100 m-0">{{ key }}</h5>
                                    </div>
                                    <div class="col-6">
                                        <button v-if="market" 
                                            type="button" class="btn mm-generic-trade-button w-100"
                                            @click="removeMarket(key)">Remove
                                        </button>
                                        <button v-else 
                                            type="button" class="btn mm-generic-trade-button w-100"
                                            @click="addMarket(key)">Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <p class="m-2">DCap and Dtop: Only displaed when markets are requested</p>
                            </div>
                            
                            <div class="col-12 mt-2">
                                <b-form-group>
                                    <b-form-checkbox value="saveMarketDefault">Set current as my default</b-form-checkbox>
                                </b-form-group>
                            </div>
                            
                            <div class="col-6 mt-1">
                                <button type="button" class="btn mm-generic-trade-button w-100" @click="onSaveMarketSetting">OK</button>
                            </div>
                            <div class="col-6 mt-1">
                                <button type="button" class="btn mm-generic-trade-button w-100" @click="onDismiss">Cancel</button>
                            </div>
                        </div>
                    </b-popover>
                    <!-- END Add market popover -->
                    <button type="button" class="btn mm-transparent-button mr-2" @click="loadChatBar()">
                        <span class="icon icon-chat"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- NB TODO REMOVE BS -->
        <b-modal v-model="modals.select_market" title="Select A Market">
            <b-row class="mt-1">
                <b-col>
                    <b-button class="w-100">Index Options</b-button>
                </b-col>
                <b-col>
                    <b-button class="w-100">EFP</b-button>
                </b-col>
            </b-row>
            <b-row class="mt-1">
                <b-col>
                    <b-button class="w-100">Single Stock Options</b-button>
                </b-col>
                <b-col>
                    <b-button class="w-100">Rolls</b-button>
                </b-col>
            </b-row>
            <b-row class="mt-1">
                <b-col>
                    <b-button class="w-100">Options Switch</b-button>
                </b-col>
                <b-col>
                    <b-button class="w-100">EFP Switch</b-button>
                </b-col>
            </b-row>
            <div slot="modal-footer" class="w-100">
                <b-button @click="modals.select_market=false">Close</b-button>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import { EventBus } from '../lib/EventBus.js';
    const Market = require('../lib/Market');
    const UserMarket = require('../lib/UserMarket');
    const UserMarketRequest = require('../lib/UserMarketRequest');
    const MarketNegotiation = require('../lib/MarketNegotiation');
    export default {
        props:{
          'markets': {
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
            }
        },
        data() {
            return {
                opened: false,
                availableSelectedMarkets: {
                    'TOP 40': false,
                    'DTOP': false,
                    'DCAP': false,
                    'SINGLES': false,
                    'Roll': false,
                },
                market_quantities: {
                    important: 1,
                    alert: 1,
                    confirm: 1,
                },
                modals: {
                    select_market: false
                }
            };
        },
        methods: {
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
            loadChatBar() {
                this.opened = (this.opened) ? false : true;
                EventBus.$emit('chatToggle', this.opened);
            },
            onOpen() {
                this.$refs.popover.$emit('open');
            },
            onDismiss() {
                this.$refs.popover.$emit('close');
            },
            onSaveMarketSetting() {
                this.$refs.popover.$emit('close');
            },
            checkSelected() {
                Object.keys(this.availableSelectedMarkets).forEach(key=>{
                    this.availableSelectedMarkets[key] = false;
                });
                this.markets.forEach((market) => {
                    this.availableSelectedMarkets[market.title] = true;
                });
            },
            addMarket(market) {
               this.markets.push( this.loadMarketData(market) );
               this.checkSelected();
            },
            removeMarket(market) {
                let index = this.markets.findIndex(function(element) {
                    console.log(element.title);
                    return element.title == market;
                });
                this.markets.splice(index , 1);
                this.checkSelected();
            },
            loadMarketData(market) {
                return new Market({
                    title: market,
                    market_requests: [
                        new UserMarketRequest({
                            attributes: {
                                expiration_date: moment("2018-03-17 00:00:00"),
                                strike: "17 000",
                                state: 'confirm',
                                bid_state: 'action',
                                offer_state: '',
                            },
                            user_markets: [
                                new UserMarket({
                                    current_market_negotiation: new MarketNegotiation({ bid: 23.3, bid_qty: 50000000, offer: 23.3, offer_qty: 50000000 })
                                }),
                                new UserMarket({
                                    current_market_negotiation: new MarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
                                })
                            ],
                            chosen_user_market: new UserMarket({
                                current_market_negotiation: new MarketNegotiation({ bid: 30, bid_qty: 50000000, offer: 25, offer_qty: 50000000 })
                            })
                        })
                    ]
                });
            },
        },
        mounted() {
           this.checkSelected();
           this.reloadQuantities();
        }
    }





</script>