<template>
<div>
    <mm-loader theme="light" :default_state="false" event_name="confirmationSubmissionLoaded" width="200" height="200"></mm-loader>
    <div v-if="confirmationLoaded && trade_confirmation">  
        <p>Thank you for your trade! Please check before accepting.</p>
        <p>Date: {{ trade_confirmation.date }} </p>
        <p>Structure: {{ trade_confirmation.trade_structure_title }}</p>
        
        <!-- Var Swap Details Confirmation -->
        <template v-if="trade_confirmation && trade_confirmation.trade_structure_slug == 'var_swap'">
            <div style="Display:inline;">
                <h3 class="text-dark">Swap</h3>
            </div>

            <table class="table table-sm">
              <thead>
                <tr>
                    <th scope="col">{{ trade_confirmation.swap_parties.initiate_org }}</th>
                    <th scope="col">{{ trade_confirmation.swap_parties.recieving_org }}</th>
                    <th scope="col">Underlying</th>
                    <th scope="col">Expiry</th>
                    <th scope="col">Volatility Level</th>
                    <th scope="col">Vega</th>
                    <th scope="col">Capped</th>
                    <th scope="col">Near Dated Future Ref</th>
                </tr>
              </thead>
              <tbody>

                <tr v-for="request_group in  trade_confirmation.request_groups">
                    <td>
                        {{ trade_confirmation.swap_parties.is_offer ? "Buys" : "Sells" }}
                    </td>
                    <td>
                        {{ trade_confirmation.swap_parties.is_offer ? "Sells" : "Buys" }}
                    </td>
                    <td>
                         {{ request_group.underlying_title }}
                    </td>
                    <td>
                        {{ request_group.expires_at }}
                    </td>
                    <td>
                         {{ trade_confirmation.volatility }}
                    </td>
                    <td>
                        {{ splitValHelper(request_group.vega,' ',3) }}
                    </td>
                    <td>
                        {{ splitValHelper(request_group.cap,' ',3) }} 
                    </td>
                    <td>
                        {{ splitValHelper(request_group.future,' ',3) }}                            
                    </td>
                </tr>
              </tbody>
            </table>
        </template>
        
        <template v-if="trade_confirmation.option_groups.length > 0">
            <div style="Display:inline;">
                <h3 class="text-dark">Option</h3>
            </div>

            <table class="table table-sm">
              <thead>
                <tr>
                    <th scope="col">{{ trade_confirmation.organisation }}</th>
                    <th scope="col">Underlying</th>
                    <th scope="col">Strike</th>
                    <th scope="col">Put/Call</th>
                    <th scope="col">Nominal</th>
                    <th scope="col">Contracts</th>
                    <th scope="col">Expiry</th>
                    <th scope="col">Volatility</th>
                    <th scope="col">Gross Prem</th>
                    <th scope="col">Net Prem</th>
                </tr>
              </thead>
              <tbody>

                <tr v-for="option_group in trade_confirmation.option_groups">
                    <td>
                      {{ (option_group.is_offer != null ? (option_group.is_offer ? "Buys" : "Sells"):'') }}
                    </td>
                    <td>
                        {{ option_group.underlying_title }}
                    </td>
                      <td>
                        {{ option_group.hasOwnProperty('strike') ? splitValHelper(option_group.strike,' ',3) : '-' }} 
                    </td>
                    <td>
                        {{ option_group.is_put ? "Put" : "Call" }} 
                    </td>
                    <td>
                        {{ option_group.hasOwnProperty('nominal') ? splitValHelper(option_group.nominal,' ',3) : "-" }}
                    </td>
                    <td>
                        {{ option_group.contracts }} 
                    </td>
                    <td>
                        {{ option_group.expires_at }}                            
                    </td>
                    <td>
                        {{ option_group.volatility }} %
                    </td>
                    <td>
                        <span v-if="option_group.gross_prem != null">{{  splitValHelper(option_group.gross_prem,' ',3) }}</span>
                    </td>
                    <td>
                        <span v-if="option_group.net_prem != null">{{  splitValHelper(option_group.net_prem,' ',3)   }}</span>
                    </td>
                </tr>
              </tbody>
            </table>
        </template>
        <template v-if="trade_confirmation.future_groups.length > 0">
            <div>
                <h3 class="text-dark">Futures</h3>
            </div>
              <table class="table table-sm">
              <thead>
                <tr>
                    <th scope="col">{{ trade_confirmation.organisation }}</th>
                    <th scope="col">Underlying</th>
                    <th scope="col">Spot</th>
                    <th scope="col">Future</th>
                    <th scope="col">Contracts</th>
                    <th scope="col">Expiry</th>
                </tr>
              </thead>
              <tbody>
                <template v-for="(future_group, key) in trade_confirmation.future_groups">
                    <!-- Used for Rolls Structure only -->
                    <template v-if="trade_confirmation && trade_confirmation.trade_structure_slug == 'rolls'">
                        <tr>
                            <td>
                              {{ (future_group.is_offer_1 != null ? (future_group.is_offer_1 ? "Buys" : "Sells"):'') }}
                            </td>
                            <td>
                              {{ future_group.underlying_title != null ? future_group.underlying_title:''  }}
                            </td>
                            <td>
                                -    
                            </td>
                            <td>
                                <input v-input-mask.number.decimal="{ precision: 2 }" class="form-control" v-model="trade_confirmation.future_groups[key]['future_1']" type="number"></input>
                                <span class="text-danger">
                                    <!-- @TODO figure out how to not hardcode the first value -->
                                <ul v-if="errors">
                                  <li class="text-danger" v-if="errors['trade_confirmation_data.structure_groups.0.items.0']" v-for="error in errors['trade_confirmation_data.structure_groups.0.items.0']">
                                      {{ error }}
                                  </li>
                                </ul>
                                </span>
                            </td>
                            <td>
                                {{ trade_confirmation.future_groups[key]['contracts'] }}
                            </td>
                            <td>
                                {{ future_group.expires_at_1 }}     
                            </td>
                        </tr>
                        <tr>
                            <td>
                              {{ (future_group.is_offer_2 != null ? (future_group.is_offer_2 ? "Buys" : "Sells"):'') }}
                            </td>
                            <td>
                              {{ future_group.underlying_title != null ? future_group.underlying_title:''  }}
                            </td>
                            <td>
                                -    
                            </td>
                            <td>
                                {{ trade_confirmation.future_groups[key]['future_2'] }}
                            </td>
                            <td>
                                {{ trade_confirmation.future_groups[key]['contracts'] }} 
                            </td>
                            <td>
                                {{ future_group.expires_at_2 }}     
                            </td>
                        </tr> 
                    </template>
                    <template v-else>
                        <tr>
                            <td>
                              {{ (future_group.is_offer != null ? (future_group.is_offer ? "Buys" : "Sells"):'') }}
                            </td>
                            <td>
                              {{ future_group.underlying_title != null ? future_group.underlying_title:''  }}
                            </td>
                            <td>
                                
                                <template v-if="trade_confirmation.future_groups[key].hasOwnProperty('spot')">
                                    <input v-input-mask.number.decimal="{ precision: 2 }" class="form-control" v-model="trade_confirmation.future_groups[key]['spot']" type="number"></input> 
                                </template>
                                <template v-else>
                                    -    
                                </template>
                            </td>
                            <td v-if="can_set_future">
                                <input v-input-mask.number.decimal="{ precision: 2 }" class="form-control" v-model="trade_confirmation.future_groups[key]['future']" type="number"></input>
                                <span class="text-danger">
                                    <!-- @TODO figure out how to not hardcode the first value -->
                                <ul v-if="errors">
                                  <li class="text-danger" v-if="errors['trade_confirmation_data.structure_groups.0.items.0']" v-for="error in errors['trade_confirmation_data.structure_groups.0.items.0']">
                                      {{ error }}
                                  </li>
                                </ul>
                                </span>
                            </td>
                            <td v-else>
                                {{ trade_confirmation.future_groups[key]['future'] }}
                            </td>
                            <td>
                                <input v-input-mask.number.decimal="{ precision: 2 }" class="mm-blue-bg form-control" v-model="trade_confirmation.future_groups[key]['contracts']" type="number"></input>
                            </td>
                            <td>
                                {{ future_group.expires_at }}     
                            </td>
                        </tr>
                    </template>
                </template>
              </tbody>
            </table>
        </template>
         <b-row>
            <b-col md="5" offset-md="7" v-if="trade_confirmation.state == 'Pending: Initiate Confirmation'">
                <button v-if="action_list.has_calculate" type="button" :disabled="!can_calc" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and calculate</button>
                <button  type="button" :disabled="!can_send" class="btn mm-generic-trade-button w-100 mb-1" @click="send()">Send to counterparty</button>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Account Booking</label>
                    <b-form-select v-model="selected_trading_account">
                        <option  v-for="trading_account in trading_accounts" :value="trading_account">{{ trading_account.safex_number }}
                        </option>
                    </b-form-select>
                    <b-row v-if="errors && errors['trading_account']" class="text-center mt-2 mb-2">
                        <b-col cols="12">
                            <p class="text-danger mb-0">{{ errors['trading_account'] }}</p>
                        </b-col>
                    </b-row>
                    <a :href="base_url+ '/trade-settings'" class="btn mm-generic-trade-button w-100 mb-1 mt-1">Edit Accounts</a>
                </div>
            </b-col>
           <b-col md="5" offset-md="7" v-else>
                <button type="button" :disabled="!can_send" class="btn mm-generic-trade-button w-100 mb-1" @click="confirm()">Im Happy, Trade Confirmed</button>
                <button v-if="action_list.has_calculate" type="button" :disabled="!can_calc" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and Calculate</button>
                <button v-if="action_list.has_dispute" type="button" :disabled="!can_dispute" class="btn mm-generic-trade-button w-100 mb-1" @click="dispute()">Send Dispute</button>

                <div class="form-group">
                    <label for="exampleFormControlSelect1">Account Booking</label>
                    <b-form-select v-model="selected_trading_account">
                        <option  v-for="trading_account in trading_accounts" :value="trading_account">{{ trading_account.safex_number }}
                        </option>
                    </b-form-select>
                    <b-row v-if="errors && errors['trading_account']" class="text-center mt-2 mb-2">
                        <b-col cols="12">
                            <p class="text-danger mb-0">{{ errors['trading_account'] }}</p>
                        </b-col>
                    </b-row>
                    <a :href="base_url+ '/trade-settings'" class="btn mm-generic-trade-button w-100 mb-1 mt-1">Edit Accounts</a>
                </div>
            </b-col>
        </b-row> 

      

    </div>
</div>
</template>

<script>
    import { EventBus } from '../../../../lib/EventBus.js';
    import TradeConfirmation from '../../../../lib/TradeConfirmation';

    export default {
      name: 'TradeConfirmationComponent',
        computed: {
            can_send:function (val) {
                return (this.action_list.has_calculate && this.action_list.has_dispute) ? (this.trade_confirmation.hasFutures() 
                    && this.trade_confirmation.hasSpots() 
                    && !this.trade_confirmation.hasChanged(this.trade_confirmation.state == 'Pending: Initiate Confirmation' ? this.oldConfirmationDataContractsExcluded : this.oldConfirmationData)
                    && this.trade_confirmation.canSend()
                ) : true;
            },
            can_calc:function (val) {
                return  this.trade_confirmation.hasFutures() 
                    &&  this.trade_confirmation.hasSpots() 
                    &&  this.trade_confirmation.hasChanged(this.oldConfirmationDataContractsExcluded, this.exclude_contracts);
            },
            can_set_future:function (val) {
                return this.trade_confirmation.trade_structure_slug != 'efp' 
                    && this.trade_confirmation.trade_structure_slug != 'efp_switch';
            },
            /**
             *   can_dispute - Checks the following to determine whether a user can dispute 
             *      
             *      1. Does the trade confo have Futures and are they set?
             *      2. Does the trade confo have Spots and are they set?
             *      3. Have the Future values changed for any of the following: 
             *          ['future','future_1','future_2','spot']
             *      4. Is any of the following states true
             *          4.1 Has been Updated and Calculated (RAN PhaseTwo again)
             *                       OR
             *          4.2 Has not been Updated and Calculated (DID NOT RUN PhaseTwo again)
             *              and have the Future Contracts changed.
             */
            can_dispute:function (val) {
                return this.action_list.has_dispute ? (this.trade_confirmation.hasFutures() 
                    && this.trade_confirmation.hasSpots() 
                    && (this.trade_confirmation.canDisputeUpdated()
                        || this.trade_confirmation.hasChanged(this.oldConfirmationDataContractsOnly, this.include_only_contracts)
                        && this.trade_confirmation.canDisputeContracts())
                    && !this.trade_confirmation.hasChanged(this.oldConfirmationDataContractsExcluded, this.exclude_contracts)
                ) : true;

            },
        },
        data() {
            return {
                trading_accounts:[],
                selected_trading_account:null,
                errors:{},
                confirmationLoaded: true,
                oldConfirmationData: null,
                oldConfirmationDataContractsExcluded: null,
                oldConfirmationDataContractsOnly: null,
                trade_confirmation: null,
                base_url: '',
                exclude_contracts: ['contracts'],
                include_only_contracts: ['future','future_1','future_2','spot'],
                action_list: {
                    has_calculate: true,
                    has_dispute: true,
                },
            }
        },
        methods: {
            loadConfirmation(tradeConfirmation)
            {
                this.trade_confirmation = tradeConfirmation;
                this.updateOldData(this.trade_confirmation);
                this.setDefaultTradingAccount();
                if(this.trade_confirmation && this.trade_confirmation.trade_structure_slug == 'var_swap') {
                    this.action_list.has_calculate = false;
                    this.action_list.has_dispute = false;
                }
            },
            clearConfirmation()
            {
                this.trade_confirmation = null;
                this.action_list.has_calculate = true;
                this.action_list.has_dispute = true;
            },
            getError(field)
            {
                if(this.errors && this.errors.hasOwnProperty(field))
                {
                    return this.errors[field][0];
                }
            },
            updateOldData(TradeConfirmation)
            {
                this.oldConfirmationData = this.trade_confirmation.prepareStore();
                this.oldConfirmationDataContractsExcluded = this.trade_confirmation.prepareStore(this.exclude_contracts);
                this.oldConfirmationDataContractsOnly = this.trade_confirmation.prepareStore(this.include_only_contracts);
            },
            getTradingAccounts: function()
            {
                axios.get(axios.defaults.baseUrl + '/trade-accounts')
                .then(response => {
                    this.trading_accounts = response.data.trading_accounts;
                    this.selected_trading_account = this.trading_accounts.find((item)=>{
                        return item.market_id == this.trade_confirmation.market_id;
                    });

                })
                .catch(err => {
                
                }); 
            },
            setDefaultTradingAccount() {
                this.selected_trading_account = this.trading_accounts.find((item)=>{
                    return item.market_id == this.trade_confirmation.underlying_id;
                });
            },
            phaseTwo: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission');
                this.confirmationLoaded = false;

                this.trade_confirmation.postPhaseTwo(this.selected_trading_account).then(response => {
                    this.errors = [];
                    this.confirmationLoaded = true;
                    this.updateOldData();

                    EventBus.$emit('loading', 'confirmationSubmission');
                })
                .catch(err => {
                    console.log("Catching error", err);
                    EventBus.$emit('loading', 'confirmationSubmission');
                    this.confirmationLoaded = true;
                    this.errors = err.errors;
                });
            },
            send: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission');
                this.confirmationLoaded = false;

               this.trade_confirmation.send(this.selected_trading_account).then(response => {
                    this.$toasted.success(response.data.message);
                    this.errors = [];
                    this.confirmationLoaded = true;
                    this.updateOldData();
                    EventBus.$emit('loading', 'confirmationSubmission');
                    this.$emit('close');
                })
                .catch(err => {
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission');
                    this.errors = err.errors;
                });  
            },
            dispute: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission');
                this.confirmationLoaded = false;

                this.trade_confirmation.dispute(this.selected_trading_account).then(response => {
                    this.updateOldData();
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission');
                    this.$emit('close');
                    this.errors = [];
                })
                .catch(err => {
                    
                    this.errors = err.errors;
                });  
            },
            confirm: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission');
                this.confirmationLoaded = false;

                this.trade_confirmation.confirm(this.selected_trading_account).then(response => {
                    EventBus.$emit('loading', 'confirmationSubmission');
                    this.updateOldData();
                    this.errors = [];
                    this.$emit('close');
                })
                .catch(err => {
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission');
                    this.errors = err.errors;
                });  
            }  
        },
        mounted() {
            this.base_url = axios.defaults.baseUrl;
            this.getTradingAccounts();
        }
    }
</script>