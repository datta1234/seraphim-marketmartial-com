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
                    <button id="popoverButton-event" type="button" class="btn mm-transparent-button mr-2" @click="onOpen">
                        <span class="icon icon-add"></span> Markets
                    </button>
                    <!-- Add market popover -->
                    <b-popover placement="bottomleft" ref="popover" target="popoverButton-event">
                        <div class="row text-center">
                            <div class="col-12">
                                <div v-for="(market,key) in availableSelectedMarkets" class="row mt-1">
                                    <div class="col-6 text-center">
                                        {{ key }}
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
    </div>
</template>

<script>
    import { EventBus } from '../lib/EventBus.js';
    const Market = require('../lib/Market');
    const UserMarket = require('../lib/UserMarket');
    export default {
        props:{
          'markets': {
            type: Array
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
            };
        },
        methods: {
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
                        markets: [
                            new UserMarket({
                                date: "Mar 20",
                                strike: "22 000",
                                bid: "21.33",
                                offer: "44.22",
                            })
                        ]
                    });
            },
        },
        mounted() {
           this.checkSelected();
        }
    }





</script>