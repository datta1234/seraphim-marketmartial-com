<template>
<div>
    <mm-loader theme="light" :default_state="false" event_name="confirmationSubmissionLoaded" width="200" height="200"></mm-loader>
    <div v-if="confirmationLoaded && trade_confirmation">  
        <p>Thank you for your trade! Please check before accepting.</p>
        <p>Date: {{ trade_confirmation.date }} </p>
        <p>Structure: {{ trade_confirmation.trade_structure_title }}</p>
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
                  {{ (option_group.is_offer != null ? (option_group.is_offer ? "Buys" : "Sell"):'') }}
                </td>
                <td>
                    {{ option_group.underlying_title }}
                </td>
                  <td>
                    {{ splitValHelper(option_group.strike,' ',3) }} 
                </td>
                <td>
                    {{ option_group.is_put ? "Put" : "Call" }} 
                </td>
                <td>
                    -   
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
            <tr v-for="(future_group, key) in trade_confirmation.future_groups">
                <td>
                  {{ (future_group.is_offer != null ? (future_group.is_offer ? "Buys" : "Sell"):'') }}
                </td>
                <td>
                  {{ future_group.underlying_title != null ? future_group.underlying_title:''  }}
                </td>
                <td>
                    
                    <template v-if="trade_confirmation.future_groups[key].hasOwnProperty('spot')">
                        <b-form-input v-model="trade_confirmation.future_groups[key]['spot']" type="number"></b-form-input> 
                    </template>
                    <template v-else>
                        -    
                    </template>
                </td>
                <td>
                    <b-form-input v-model="trade_confirmation.future_groups[key]['future']" type="number"></b-form-input>
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
                    <b-form-input class="mm-blue-bg" v-model="trade_confirmation.future_groups[key]['contracts']" type="number"></b-form-input>
                </td>
                <td>
                    {{ future_group.expires_at }}     
                </td>
            </tr>
          </tbody>
        </table>
         <b-row>
            <b-col md="5" offset-md="7" v-if="trade_confirmation.status_id == 1">
                <button type="button" :disabled="!can_calc" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and calculate</button>
                <button  type="button" :disabled="!can_send" class="btn mm-generic-trade-button w-100 mb-1" @click="send()">Send to counterparty</button>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Account Booking</label>
                    <b-form-select :disabled="can_send" v-model="selected_trading_account">
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
                <button type="button" :disabled="!can_calc" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and Calculate</button>
                <button type="button" :disabled="!can_dispute" class="btn mm-generic-trade-button w-100 mb-1" @click="dispute()">Send Dispute</button>

                <div class="form-group">
                    <label for="exampleFormControlSelect1">Account Booking</label>
                    <b-form-select v-model="selected_trading_account">
                        <option  v-for="trading_account in trading_accounts" :value="trading_account">{{ trading_account.safex_number }}
                        </option>
                    </b-form-select>
                    <a :href="base_url + '/trade-settings'" class="btn mm-generic-trade-button w-100 mb-1 mt-1">Edit Accounts</a>
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
                return  this.trade_confirmation.hasFutures() && this.trade_confirmation.hasSpots() && JSON.stringify(this.oldConfirmationData) == JSON.stringify(this.trade_confirmation.prepareStore());
            },
            can_calc:function (val) {
                console.log("Checking this: ",this.trade_confirmation);
                return this.trade_confirmation.hasFutures() && this.trade_confirmation.hasSpots() &&  JSON.stringify(this.oldConfirmationData) != JSON.stringify(this.trade_confirmation.prepareStore());
            }
        },
        data() {
            return {
                trading_accounts:[],
                selected_trading_account:null,
                errors:{},
                confirmationLoaded: true,
                oldConfirmationData: null,
                trade_confirmation: null,
                can_dispute: true,
                base_url: '',
            }
        },
        methods: {
            loadConfirmation(tradeConfirmation)
            {
                this.trade_confirmation = tradeConfirmation;
                this.updateOldData(this.trade_confirmation)
            },
            clearConfirmation()
            {
                this.trade_confirmation = null; 
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
            phaseTwo: function()
            {
                EventBus.$emit('loading', 'confirmationSubmission');
                this.confirmationLoaded = false;

                this.trade_confirmation.postPhaseTwo(this.selected_trading_account).then(response => {
                    console.log("HITTING THIS", response);
                    this.errors = [];
                    this.confirmationLoaded = true;
                    this.updateOldData();
                   
                    if(trade_confirmation.status_id != 1)
                    {
                       this.can_dispute = true; 
                    }

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
                console.log("h1");
                EventBus.$emit('loading', 'confirmationSubmission');
                this.confirmationLoaded = false;

                console.log("h2");
                this.trade_confirmation.dispute(this.selected_trading_account).then(response => {
                    console.log("h5");
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