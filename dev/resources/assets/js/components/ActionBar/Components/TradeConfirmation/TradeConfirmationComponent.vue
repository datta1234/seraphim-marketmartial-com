<template>
<div>  
    <p>Thank you for your trade! Please check before accepting.</p>
    <p>Date: {{ trade_confirmation.organisation }} </p>
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
              {{ option_group.is_offer ? "Sell" : "Buy" }}
            </td>
            <td>
                {{ option_group.underlying_title }}
            </td>
              <td>
                {{ option_group.strike }} 
            </td>
            <td>
                {{ option_group.put_call }} 
            </td>
            <td>
                {{ option_group.quantity }} 
            </td>
            <td>
                {{ option_group.quantity }} 
            </td>
            <td>
                {{ option_group.expires_at }}                            
            </td>
            <td>
                {{ option_group.volatility }}
            </td>
            <td>
                {{ option_group.gross_premiums }}
            </td>
            <td>
                {{ option_group.net_premiums }}
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
        <tr v-for="future_group in trade_confirmation.future_groups">
            <td>
                {{ future_group.is_offer ? "Sell" : "Buy" }}
            </td>
            <td>

            </td>
            <td>
                           
            </td>
            <td>
                <b-form-input v-model="proposed_trade_confirmation.spot_price" type="text"></b-form-input>
            </td>
            <td>
                <b-form-input v-model="proposed_trade_confirmation.future_reference" type="text"></b-form-input>
            </td>
            <td>
                {{ future_group.expires_at }}     
            </td>
        </tr>
      </tbody>
    </table>

    <b-row>
        <b-col md="5" offset-md="7">
            <button type="button" class="btn mm-generic-trade-button w-100 mb-1">Update and calculate</button>
            <button type="button" class="btn mm-generic-trade-button w-100 mb-1">Send to counterparty</button>
             <div class="form-group">
                <label for="exampleFormControlSelect1">Account Booking</label>
                <select class="form-control" id="exampleFormControlSelect1">
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                </select>
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
      props:{
          'trade_confirmation': {
            type: Object
          }
        },
        data() {
            return {
                proposed_trade_confirmation: new TradeConfirmation()
            };
        },
        methods: {
            phaseOne: function()
            {
                this.trade_confirmation.phaseOne();   
            }
           
        },
        mounted() {
            
        }
    }
</script>