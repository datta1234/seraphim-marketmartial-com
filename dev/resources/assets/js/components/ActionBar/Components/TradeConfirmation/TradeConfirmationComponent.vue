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
                        {{ splitValHelper(trade_confirmation.vega,' ',3) }}
                    </td>
                    <td>
                        {{ splitValHelper(request_group.cap,' ',3) }} 
                    </td>
                    <td>
                        {{ request_group.future ? splitValHelper(request_group.future,' ',3) : '-' }}                            
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
                </tr>
              </thead>
              <tbody>

                <tr v-for="(option_group, key) in trade_confirmation.option_groups">
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
                        <!-- <div v-if="hasOldValue('option_groups',key,'is_put')" class="font-weight-bold text-danger modal-info-text">
                            Calculated value : {{ option_group.is_put_old ? "Put" : "Call" }}.
                        </div> -->
                    </td>
                    <td>
                        {{ option_group.hasOwnProperty('nominal') ? splitValHelper(option_group.nominal,' ',3) : "-" }}
                    </td>
                    <td>
                        {{ option_group.contracts }}
                        <!-- <div v-if="hasOldValue('option_groups',key,'contracts')" class="font-weight-bold text-danger modal-info-text">
                            Calculated value : {{ option_group.contracts_old }}.
                        </div> -->
                    </td>
                    <td>
                        {{ option_group.expires_at }}                            
                    </td>
                    <td>
                        {{ option_group.volatility }} %
                    </td>
                    <td>
                        <span v-if="option_group.gross_prem != null">{{  splitValHelper(option_group.gross_prem,' ',3) }}</span>
                        <!-- <div v-if="hasOldValue('option_groups',key,'gross_prem')" class="font-weight-bold text-danger modal-info-text">
                            Calculated value : {{  splitValHelper(option_group.gross_prem_old,' ',3) }}.
                        </div> -->
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
                    <th scope="col">Spot {{ trade_confirmation.market_id == 4 ? '(ZAR)' : '' }}</th>
                    <th scope="col">Future {{ trade_confirmation.market_id == 4 ? '(ZAR)' : '' }}</th>
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
                                <input  v-input-mask.number.decimal="{ precision: 2 }" 
                                        class="form-control" 
                                        v-model="trade_confirmation.future_groups[key]['future_1']" 
                                        type="number"
                                        v-bind:class="{ 'is-invalid': inputState(key,'future_1') }">
                                </input>
                                <div v-if="hasOldValue('future_groups',key,'future_1')" class="font-weight-bold text-danger modal-info-text">
                                    Previous value : {{  splitValHelper(future_group.future_1_old,' ',3) }}.
                                </div>
                            </td>
                            <td>
                                {{ future_group.contracts }}
                                <div v-if="hasOldValue('future_groups',key,'contracts')" class="font-weight-bold text-danger modal-info-text">
                                    Calculated value : {{ future_group.contracts_old }}.
                                </div>
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
                                {{ future_group.future_2 }}
                                <div v-if="hasOldValue('future_groups',key,'future_2')" class="font-weight-bold text-danger modal-info-text">
                                    Previous value : {{ future_group.future_2_old }}.
                                </div>
                            </td>
                            {{ future_group.contracts }}
                                <div v-if="hasOldValue('future_groups',key,'contracts')" class="font-weight-bold text-danger modal-info-text">
                                    Calculated value : {{ future_group.contracts_old }}.
                                </div>
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
                                    <input  v-input-mask.number.decimal="{ precision: 2 }"
                                            class="form-control" 
                                            v-model="trade_confirmation.future_groups[key]['spot']" 
                                            type="number"
                                            v-bind:class="{ 'is-invalid': inputState(key,'spot') }">
                                    </input>
                                    <div v-if="hasOldValue('future_groups',key,'spot')" class="font-weight-bold text-danger modal-info-text">
                                        Previous value : {{ splitValHelper(future_group.spot_old,' ',3) }}.
                                    </div>
                                </template>
                                <template v-else>
                                    -    
                                </template>
                            </td>
                            <td v-if="can_set_future">
                                <input  v-input-mask.number.decimal="{ precision: 2 }"
                                        class="form-control" v-model="trade_confirmation.future_groups[key]['future']" 
                                        type="number"
                                        v-bind:class="{ 'is-invalid': inputState(key,'future') }">
                                </input>
                                <div v-if="hasOldValue('future_groups',key,'future')" class="font-weight-bold text-danger modal-info-text">
                                    Previous value : {{ splitValHelper(future_group.future_old,' ',3) }}.
                                </div>
                            </td>
                            <td v-else>
                                {{ splitValHelper(future_group.future,' ',3) }}
                                <div v-if="hasOldValue('future_groups',key,'future')" class="font-weight-bold text-danger modal-info-text">
                                    Calculated value : {{ splitValHelper(future_group.future_old,' ',3) }}.
                                </div>
                            </td>
                            <td v-if="trade_confirmation.trade_structure_slug == 'efp' 
                                || trade_confirmation.trade_structure_slug == 'efp_switch'">
                                {{ future_group.contracts }}
                                <div v-if="hasOldValue('future_groups',key,'contracts')" class="font-weight-bold text-danger modal-info-text">
                                    Calculated value : {{ future_group.contracts_old }}.
                                </div>
                            </td>
                            <td v-else>
                                <input  v-input-mask.number.decimal="{ precision: 2 }" 
                                        class="mm-blue-bg form-control" 
                                        v-model="trade_confirmation.future_groups[key]['contracts']" 
                                        type="number"
                                        v-bind:class="{ 'is-invalid': inputState(key,'contract') }">
                                </input>
                                <div v-if="hasOldValue('future_groups',key,'contracts')" class="font-weight-bold text-danger modal-info-text">
                                    Calculated value : {{ future_group.contracts_old }}.
                                </div>
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
        
        <template v-if="trade_confirmation">
            <div style="Display:inline;">
                <h3 class="text-dark">Fees</h3>
            </div>
        </template>

        <b-row>
            <b-col md="6">
                <template v-if="trade_confirmation && trade_confirmation.fee_groups.length > 0">
                    <b-row>
                        <!-- Confirmation Fee -->
                        <b-col md="12">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th scope="col">Calculated Fee</th></tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(fee_groups, key) in trade_confirmation.fee_groups">
                                        <td>
                                            {{ (typeof fee_groups.fee_total !== "undefined" && fee_groups.fee_total !== null ) ? splitValHelper(fee_groups.fee_total,' ',3) : '-' }}
                                        </td>
                                    </tr>
                              </tbody>
                            </table>
                        </b-col>
                    </b-row>
                </template>

                <b-row>
                    <b-col md="10" offset="2">
                        <b-row v-if="new_errors.messages.length > 0" class="mt-4">
                            <b-col cols="12">
                                <ul>
                                    <li :key="index" v-for="(error, index) in new_errors.messages">
                                        <p class="text-danger mb-0">{{ error }}</p>
                                    </li>
                                </ul>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
            <b-col md="6">
                <b-col md="10" offset-md="2" v-if="trade_confirmation.state == 'Pending: Initiate Confirmation'">
                    <button v-active-request v-if="action_list.has_calculate" type="button" :disabled="!can_calc" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and calculate</button>
                    <button v-active-request type="button" :disabled="!can_send" class="btn mm-generic-trade-button w-100 mb-1" @click="send()">Send to counterparty</button>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Account Booking</label>
                        <b-form-select v-model="selected_trading_account">
                            <option  v-for="trading_account in trading_accounts" :value="trading_account">
                                {{ trading_account.sub_account ? trading_account.sub_account : trading_account.safex_number }}
                            </option>
                        </b-form-select>
                        <b-row v-if="errors && errors['trading_account_id']" class="text-center mt-2 mb-2">
                            <b-col cols="12">
                                <p class="text-danger mb-0">{{ errors['trading_account_id'] }}</p>
                            </b-col>
                        </b-row>
                        <a :href="base_url+ '/trade-settings'" class="btn mm-generic-trade-button w-100 mb-1 mt-1">Edit Accounts</a>
                    </div>
                </b-col>
               <b-col md="10" offset-md="2" v-else>
                    <button v-active-request type="button" :disabled="!can_send" class="btn mm-generic-trade-button w-100 mb-1" @click="confirm()">Im Happy, Trade Confirmed</button>
                    <button v-active-request v-if="action_list.has_calculate" type="button" :disabled="!can_calc" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and Calculate</button>
                    <button v-active-request v-if="action_list.has_dispute" type="button" :disabled="!can_dispute" class="btn mm-generic-trade-button w-100 mb-1" @click="dispute()">Send Dispute</button>

                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Account Booking</label>
                        <b-form-select v-model="selected_trading_account">
                            <option  v-for="trading_account in trading_accounts" :value="trading_account">{{ trading_account.sub_account ? trading_account.sub_account : trading_account.safex_number }}
                            </option>
                        </b-form-select>
                        <b-row v-if="errors && errors['trading_account_id']" class="text-center mt-2 mb-2">
                            <b-col cols="12">
                                <p class="text-danger mb-0">{{ errors['trading_account_id'] }}</p>
                            </b-col>
                        </b-row>
                        <a :href="base_url+ '/trade-settings'" class="btn mm-generic-trade-button w-100 mb-1 mt-1">Edit Accounts</a>
                    </div>
                </b-col>
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
                new_errors: {
                    fields: [],
                    messages: [],
                },
            }
        },
        methods: {
            /**
             * Toggles input states when there are errors for the input
             *
             * @param {number} index - the index of the input
             * @param {string} field - the input field
             */
            inputState(index, field) {
                return ( (this.new_errors.fields.indexOf(index +'.'+ field) != -1) 
                    || (this.trade_confirmation.future_groups[index][field+'_old'] != null) )? true: false;
            },
            hasOldValue(group,index,field) {
                return (this.trade_confirmation[group][index].hasOwnProperty(field+'_old'))
                    && (this.trade_confirmation[group][index][field+'_old'] != null);     
            },
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
                })
                .catch(err => {
                    //console.error(err);
                }); 
            },
            setDefaultTradingAccount()
            {
                this.selected_trading_account = this.trading_accounts.find((item) => {
                    return typeof item.market_id !== 'undefined' && item.market_id == this.trade_confirmation.underlying_id;
                });
            },
            phaseTwo: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission', true);
                this.confirmationLoaded = false;

                this.trade_confirmation.postPhaseTwo(this.selected_trading_account).then(response => {
                    this.loadConfirmation(new TradeConfirmation(response.data.data));

                    this.new_errors.fields = [];
                    this.new_errors.messages = [];

                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                })
                .catch(err => {
                    //console.error(err);
                    this.loadErrors(err.errors);
                    this.$toasted.error(err.message);
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                });
            },
            send: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission', true);
                this.confirmationLoaded = false;

               this.trade_confirmation.send(this.selected_trading_account).then(response => {
                    this.$emit('close');
                    this.loadConfirmation(new TradeConfirmation(response.data.data));
                    this.$toasted.success(response.data.message);

                    this.new_errors.fields = [];
                    this.new_errors.messages = [];

                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                })
                .catch(err => {
                    //console.error(err);
                    this.loadErrors(err.errors);
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                });  
            },
            dispute: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission', true);
                this.confirmationLoaded = false;

                this.trade_confirmation.dispute(this.selected_trading_account).then(response => {
                    this.$emit('close');
                    this.loadConfirmation(new TradeConfirmation(response.data.data));

                    this.new_errors.fields = [];
                    this.new_errors.messages = [];

                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                })
                .catch(err => {
                    //console.error(err);
                    this.loadErrors(err.errors);
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                });  
            },
            confirm: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission', true);
                this.confirmationLoaded = false;

                this.trade_confirmation.confirm(this.selected_trading_account).then(response => {
                    this.$emit('close');
                    this.loadConfirmation(new TradeConfirmation(response.data.data));
                    this.new_errors.fields = [];
                    this.new_errors.messages = [];
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                })
                .catch(err => {
                    //console.error(err);
                    this.loadErrors(err.errors);
                    this.confirmationLoaded = true;
                    EventBus.$emit('loading', 'confirmationSubmission', false);
                });  
            },
            /*{
              "message": "The given data was invalid.",
              "errors": {
                "trade_confirmation_data.structure_groups.0.items.1": [
                  {
                    "contract": "please enter a valid Contracts value"
                  }
                ]
              }
            }*/
            loadErrors(errors) {
                Object.keys(errors).forEach(error_key => {
                    let error_key_array = error_key.split('.');
                    let groups_index = error_key_array.findIndex(x=>{return x == 'structure_groups'});
                    let group = '';
                    if(groups_index !== -1 && error_key_array.length > groups_index) {
                        group += error_key_array[groups_index+1];
                    }

                    errors[error_key].forEach(err=>{
                        let item_key = (err.constructor == String ? error_key : Object.keys(err)[0]);
                        let item = (err.constructor == String ? err : err[item_key]);

                        if(this.new_errors.fields.findIndex(x=>{return x == group+'.'+item_key}) == -1  ) {
                            this.new_errors.fields.push(group+'.'+item_key);
                        }

                        if(this.new_errors.messages.findIndex(x=>{return x == item}) == -1  ) {
                            this.new_errors.messages.push(item);
                        }
                    });
                });
            }
        },
        mounted() {
            this.base_url = axios.defaults.baseUrl;
            this.getTradingAccounts();
            this.$on('hide', this.clearConfirmation);
        }
    }
</script>