<template>
<div>  
    <p>Thank you for your trade! Please check before accepting.</p>
    <p>Date: {{ trade_confirmation.date }} </p>
    <p>Structure: {{ trade_confirmation.trade_structure_title }}</p>
    <div style="Display:inline;">
        <h3>Option</h3>
    </div>

    <table class="table table-sm">
      <thead>
        <tr>
            <th scope="col">Bank</th>
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
              {{ (option_group.is_offer != null ? (option_group.is_offer ? "Sell" : "Buy"):'') }}
            </td>
            <td>
                {{ option_group.underlying_title }}
            </td>
              <td>
                {{ option_group.strike }} 
            </td>
            <td>
                {{ option_group.is_put ? "Put" : "call" }} 
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
                {{ option_group.volatility }}
            </td>
            <td>
                {{ option_group.gross_prem }}
            </td>
            <td>
                {{ option_group.net_prem }}
            </td>
        </tr>
      </tbody>
    </table>

    <div>
        <h3>Futures</h3>
    </div>
      <table class="table table-sm">
      <thead>
        <tr>
            <th scope="col">Bank</th>
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
              {{ (future_group.is_offer != null ? (future_group.is_offer ? "Sell" : "Buy"):'') }}
            </td>
            <td>
              {{ future_group.underlying_title != null ? future_group.underlying_title:''  }}
            </td>
            <td>
                 - 
               <!--   <b-form-input v-model="trade_confirmation.future_groups[key]['spot']" type="text"></b-form-input> -->
         
            </td>
            <td>
                <b-form-input v-model="trade_confirmation.future_groups[key]['future']" type="number"></b-form-input>
                <span class="text-danger">
                    <!-- @TODO figure out how to not hardcode the first value -->
              <ul v-if="errors">
                  <li class="text-danger" v-if="errors['structure_groups.0.items.0']" v-for="error in errors['structure_groups.0.items.0']">
                      {{ error }}
                  </li>  
                </ul>
                </span>
            </td>
            <td>
                <b-form-input :disabled="true" v-model="trade_confirmation.future_groups[key]['contracts']" type="number"></b-form-input>
            </td>
            <td>
                {{ future_group.expires_at }}     
            </td>
        </tr>
      </tbody>
    </table>
   
     <b-row>
        <b-col md="5" offset-md="7" v-if="trade_confirmation.status_id == 1">
            <button type="button" :disabled="!can_proceed" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and calculate</button>
            <button  type="button" :disabled="!can_proceed" class="btn mm-generic-trade-button w-100 mb-1" @click="send()">Send to counterparty</button>
             <div class="form-group">
                <label for="exampleFormControlSelect1">Account Booking</label>
                <b-form-select :disabled="!can_proceed" v-model="selected_trading_account">
                        <option  v-for="trading_account in trading_accounts" :value="trading_account">{{ trading_account.safex_number }}
                        </option>
                  </b-form-select>
              </div>
        </b-col>
       <b-col md="5" offset-md="7" v-else>
            <button type="button" :disabled="!can_proceed" class="btn mm-generic-trade-button w-100 mb-1" @click="confirm()">Im Happy, Trade Confirmed</button>
            <button  type="button" :disabled="!can_proceed" class="btn mm-generic-trade-button w-100 mb-1" @click="phaseTwo()">Update and Calculate</button>
             <button  type="button" :disabled="!can_proceed" class="btn mm-generic-trade-button w-100 mb-1" @click="dispute()">Send Dispute</button>

             <div class="form-group">
                <label for="exampleFormControlSelect1">Account Booking</label>
                <b-form-select :disabled="!can_proceed" v-model="selected_trading_account">
                        <option  v-for="trading_account in trading_accounts" :value="trading_account">{{ trading_account.safex_number }}
                        </option>
                  </b-form-select>
              </div>
        </b-col>
    </b-row> 
</div>
</template>

<script>
    import { EventBus } from '../../../../lib/EventBus.js';
    import TradeConfirmation from '../../../../lib/TradeConfirmation';

    export default {
      name: 'TradeConfirmationComponent',
        computed: {
            can_proceed:function (val) {

            let confirmation = this.trade_confirmation;
            let future_groups = confirmation.future_groups;            
            return future_groups.reduce((out, group)=>{
                if(group.future.length == 0)
                {
                    out = false;
                }
                return out;
            }, true);
        },
    },
      data() {
        return {
                trading_accounts:[],
                selected_trading_account:null,
                errors:{}
            }
        },
      props:{
          'trade_confirmation': {
            type: Object
          }
        },
        methods: {
            getError(field)
            {
                if(this.errors && this.errors.hasOwnProperty(field))
                {
                    return this.errors[field][0];
                }
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
                this.trade_confirmation.postPhaseTwo().then(response => {
                    this.errors = [];
                })
                .catch(err => {
                    this.errors = err.errors;
                });
            },
            send: function()
            {
               this.trade_confirmation.send(this.selected_trading_account).then(response => {
                    this.errors = [];
                    this.$emit('close');
                })
                .catch(err => {
                    this.errors = err.errors;
                });  
            },
            dispute: function()
            {
                
                this.trade_confirmation.dispute(this.selected_trading_account).then(response => {
                    this.$emit('close');
                    this.errors = [];
                })
                .catch(err => {
                    
                    this.errors = err.errors;
                });  
            },
            confirm: function()
            {
                
                this.trade_confirmation.confirm(this.selected_trading_account).then(response => {
                    this.errors = [];
                    this.$emit('close');
                })
                .catch(err => {
                    
                    this.errors = err.errors;
                });  
            }  
        },
        mounted() {
           this.getTradingAccounts();
        }
    }
</script>