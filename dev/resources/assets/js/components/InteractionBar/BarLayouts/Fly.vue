<template>
    <b-container fluid dusk="ibar-negotiation-bar-fly">

        <ibar-user-market-title :title="market_title" :time="market_time" class="mt-1 mb-2"></ibar-user-market-title>
        
        <ibar-market-requested class="mb-2" 
            :market-request="marketRequest" 
            :columns="market_requested_columns">
        </ibar-market-requested>
        
        <ibar-market-requested-futures class="mb-2" 
            :market-request="marketRequest" 
            :columns="market_requested_future_columns">
        </ibar-market-requested-futures>
        
        <!-- VOL SPREAD History - Market-->
        <ibar-negotiation-history-market 
         :message="history_message"
         :history="marketRequest.quotes" 
         :market-request="marketRequest"
         v-if="marketRequest.quotes && !marketRequest.chosen_user_market" 
         class="mb-3"
         @update-on-hold="setUpProposal" 
         @update-on-accept="setUpProposal"
        >
        </ibar-negotiation-history-market>
   
        <!-- Contracts History - Trade-->
        <ibar-negotiation-history-contracts :message="history_message" :history="marketRequest.chosen_user_market.market_negotiations" v-if="marketRequest.chosen_user_market" class="mb-2"></ibar-negotiation-history-contracts>

    <template v-if="!negotiation_available && cant_amend && !$root.is_admin && !$root.is_viewer">
        <b-row>
            <b-col cols="10">
                <p class="text-center">
                    Cannot Amend Levels
                </p>
            </b-col>
        </b-row>
    </template>
     <template v-if="negotiation_available">
        <ibar-trade-at-best-negotiation 
         v-if="!can_negotiate && is_trading_at_best"
         :check-invalid="check_invalid" 
         :current-negotiation="last_negotiation" 
         :market-negotiation="proposed_user_market_negotiation"
         :root-negotiation="marketRequest.chosen_user_market.trading_at_best">
        </ibar-trade-at-best-negotiation>
    </template>
    <template v-if="(!is_trading || is_trading_at_best) && negotiation_available">
        
        <ibar-volatility-field v-if="!marketRequest.chosen_user_market && trade_group_1.choice" :user-market="proposed_user_market" :trade-group="trade_group_1"></ibar-volatility-field>
        <ibar-market-negotiation-contracts 
            class="mb-1" v-if="can_negotiate" 
            @validate-proposal="validateProposal" 
            :disabled="conditionActive('fok') || meet_in_the_middle_proposed" 
            :check-invalid="check_invalid" 
            :current-negotiation="last_negotiation" 
            :market-negotiation="proposed_user_market_negotiation"
            :is-quote-phase="is_quote_phase"
        >
        </ibar-market-negotiation-contracts>
        <ibar-volatility-field v-if="!marketRequest.chosen_user_market && trade_group_2.choice" :user-market="proposed_user_market" :trade-group="trade_group_2"></ibar-volatility-field>

    </template>
    <!-- Alert me when cleared -->
    <alert-cleared v-if="!can_negotiate && !$root.is_admin && !$root.is_viewer" :market_request="marketRequest"></alert-cleared>
    <template v-if="(!is_trading || is_trading_at_best) && negotiation_available && !$root.is_viewer">
        
        <b-row class="mb-1">
            <b-col cols="10">
                <b-col cols="12" v-for="(error,key) in errors" :key="key" class="text-danger">
                    {{ error[0] }}
                </b-col>
                <ibar-remove-conditions  v-if="can_negotiate" :market-negotiation="proposed_user_market_negotiation"></ibar-remove-conditions>
                <b-row class="justify-content-md-center mb-1">
                    <b-col cols="6">

                        <!-- || (maker_quote && !is_on_hold) && ( !maker_quote|| (maker_quote && !maker_quote.is_repeat)) -->
                       

                        
                         <b-button v-active-request v-if="maker_quote || is_on_hold || (maker_quote && maker_quote.is_repeat)" class="w-100 mt-1" :disabled="check_invalid || server_loading" size="sm" dusk="ibar-action-amend" variant="primary" @click="amendQuote()">Amend</b-button>


                        <b-button v-active-request v-if="is_on_hold && (maker_quote && !maker_quote.is_repeat)" class="w-100 mt-1" :disabled="server_loading" size="sm" dusk="ibar-action-repeat" variant="primary" @click="repeatQuote()">Repeat</b-button>

                        <b-button v-active-request v-if="maker_quote || is_on_hold || (maker_quote && maker_quote.is_repeat)" class="w-100 mt-1" :disabled="server_loading" size="sm" dusk="ibar-action-pull" variant="primary" v-b-modal.pullQuote>Pull</b-button>

                        <!-- Modal Component -->
                        <b-modal ref="pullModal" id="pullQuote" title="Pull Market" class="mm-modal mx-auto">
                            <p>Are you sure you want to pull this quote?</p>
                            <div slot="modal-footer" class="w-100">
                                <b-row align-v="center">
                                    <b-col cols="12">
                                        <b-button v-active-request class="mm-modal-button mr-2 w-25" @click="pullQuote()">Pull</b-button>
                                        <b-button v-active-request class="btn mm-modal-button ml-2 w-25 btn-secondary" @click="hideModal()">Cancel</b-button>
                                    </b-col>
                                </b-row>
                            </div>
                        </b-modal>


                    </b-col>
                </b-row>
                <b-row class="justify-content-md-center" v-if="!maker_quote && !marketRequest.chosen_user_market">
                    <b-col cols="6">

                        <b-button v-active-request class="w-100 mt-1"
                          v-if="!maker_quote" 
                          :disabled="check_invalid || server_loading" 
                          size="sm" 
                          dusk="ibar-action-send" 
                          variant="primary" 
                          @click="sendQuote()">
                            Send
                        </b-button>
                        

                    </b-col>
                </b-row>

                 <b-row class="justify-content-md-center" v-if="marketRequest.chosen_user_market && can_negotiate">
                    <b-col cols="6">
                         
                        <b-button v-active-request class="w-100 mt-1" 
                         :disabled="check_invalid || server_loading || conditionActive('fok')" 
                         size="sm" 
                         dusk="ibar-action-send" 
                         variant="primary" 
                         @click="sendNegotiation()">
                                 {{ ( proposed_user_market_negotiation.id != null ? 'Amend' : 'Send' ) }}
                        </b-button>
                        <b-button v-active-request class="w-100 mt-1" 
                         :disabled="conditionActive('fok') || !can_click_spin" 
                         v-if="can_spin" 
                         size="sm" 
                         dusk="ibar-action-send" 
                         variant="primary" 
                         @click="spinNegotiation()">
                            Spin
                        </b-button>
                    </b-col>
                </b-row>
                
                <b-row class="justify-content-md-center" v-if="marketRequest.chosen_user_market && is_trading_at_best && !is_trading_at_best_closed">
                    <b-col cols="6">
                         
                        <b-button v-active-request class="w-100 mt-1" 
                         :disabled="check_invalid || server_loading" 
                         size="sm" 
                         dusk="ibar-action-send" 
                         variant="primary" 
                         @click="improveBestNegotiation()">
                                Send
                        </b-button>
                    </b-col>
                </b-row>


                <b-row class="justify-content-md-center">
                    <b-col cols="6">
                        <!-- !maker_quote && !marketRequest.chosen_user_market -->
                         <b-button v-active-request v-if="can_disregard && !in_no_cares" class="w-100 mt-1" size="sm" dusk="ibar-action-nocares" variant="secondary" @click="addToNoCares()">No Cares</b-button>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
        <ibar-apply-conditions v-if="can_negotiate && !conditionActive('fok')" class="mb-2 mt-2" :market-negotiation="proposed_user_market_negotiation" :market-request="marketRequest"></ibar-apply-conditions>
    </template>
    
            

    <ibar-trade-counter-desired-quantity v-if="is_trading && !is_trading_at_best" :market-request="marketRequest"></ibar-trade-counter-desired-quantity>
    <ibar-trade-work-balance v-if="mustWorkBalance" :market-request="marketRequest"></ibar-trade-work-balance>

        <!-- <b-row class="mb-2">
            <b-col>
                <b-form-checkbox v-model="state_premium_calc" value="true" unchecked-value="false"> Apply premium calculator</b-form-checkbox>
            </b-col>
        </b-row> -->

      <!--   <ibar-apply-premium-calculator  v-if="can_negotiate" :market-negotiatio="proposed_user_market_negotiation"></ibar-apply-premium-calculator> -->
        
        <ibar-active-conditions class="mt-2" v-if="marketRequest.chosen_user_market != null" 
        :user-market="marketRequest.chosen_user_market" 
        :conditions="marketRequest.chosen_user_market.active_conditions"
        :sent_conditions="marketRequest.chosen_user_market.sent_conditions"

        ></ibar-active-conditions>

    </b-container>
</template>
<script>
    import { EventBus } from '~/lib/EventBus.js';
    import UserMarketRequest from '~/lib/UserMarketRequest';
    import UserMarketNegotiation from '~/lib/UserMarketNegotiation';
    import UserMarket from '~/lib/UserMarket';
    
    import moment from 'moment';

    import IbarApplyConditions from '../MarketComponents/ApplyConditionsComponent';
    import IbarRemoveConditions from '../MarketComponents/RemoveConditionsComponent';
    import IbarActiveConditions from '../MarketComponents/ActiveConditions';
    import IbarVolatilityField from '../MarketComponents/VolatilityField';
    import IbarMarketRequested from '../MarketComponents/MarketRequested';
    import IbarTradeAtBestNegotiation from '../TradeComponents/TradingAtBestNegotiation.vue';
    import IbarMarketRequestedFutures from '../MarketComponents/MarketRequestedFutures.vue';

    import AlertCleared from '../Components/AlertClearedComponent.vue';

    import NegotiationBarMixin from '../NegotiationBarMixin';

    export default {
        mixins:[NegotiationBarMixin],
        components: {
            IbarApplyConditions,
            IbarRemoveConditions,
            IbarActiveConditions,
            IbarVolatilityField,
            IbarMarketRequested,
            IbarTradeAtBestNegotiation,
            AlertCleared,
            IbarMarketRequestedFutures
        },
        props: {
            
        },
        data() {
            return {
                state_premium_calc: false,
                market_request_subscribe: false,
                user_market: null,
                market_history: [],

                proposed_user_market: new UserMarket(),
                proposed_user_market_negotiation: new UserMarketNegotiation(),

                default_user_market_negotiation:new UserMarketNegotiation(),
                history_message: null,

                removable_conditions: [],
                server_loading: false,
                check_invalid: false,
                errors: [],
                showMessagesIn: [
                    "market_request_store",
                    "market_request_update",
                    "market_request_delete",
                    "market_negotiation_store"
                ],
                market_requested_columns: [
                    'quantity',
                    'strike',
                    'status',
                ],
                market_requested_future_columns: [
                    'future',
                ]
            };
        },
        
        computed: {
            'trade_group_1': function() {
                let group = this.$root.config("trade_structure.fly.group_1");
                return this.marketRequest.trade_items[group];
            },
            'trade_group_2': function() {
                let group = this.$root.config("trade_structure.fly.group_3");
                return this.marketRequest.trade_items[group];
            },
            'market_title': function() {
                return [
                    this.marketRequest.trade_items.default.tradable.title,
                    this.marketRequest.trade_items.default[this.$root.config("trade_structure.fly.expiration_date")],
                    this.marketRequest.trade_structure
                ].join(' ');
            },
        },
        methods: {
            
        },
        mounted() {
            this.init();
            EventBus.$on('notifyUser',this.updateMessage);
        }
    }
</script>