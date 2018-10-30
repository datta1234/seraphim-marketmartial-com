import BaseModel from './BaseModel';

import OptionGroup from './tradeconfirmations/OptionGroup';
import FutureGroup from './tradeconfirmations/FutureGroup';

export default class TradeConfirmation extends BaseModel {

    constructor(options,fees) {

        super({
            _used_model_list: [OptionGroup,FutureGroup],
            _relations:{
                option_groups: {
                    addMethod: (option_groups) => { 
                        this.addOptionGroups(option_groups) 
                    },
                    setMethod: (option_groups) => { 
                        this.setOptionGroups(option_groups) 
                    },
                },
                future_groups: {
                    addMethod: (future_groups) => { 
                        this.addFutureGroups(future_groups) 
                    },
                    setMethod: (structure_groups) => { 
                        this.setFutureGroups(future_groups) 
                    },
                }
            }
        })

        this.option_groups = [];
        this.future_groups = [];

        const defaults = {
                id : "",
                organisation : "",
                trade_structure_title : "",
                volatility : "",
                structure_groups : "",
                market_id : "",
                market_type_id : "",
                market_request_id : "",
                market_request_title : "",
                underlying_id : "",
                underlying_title : "",
                is_single_stock : "",
                traded_at : "",
                is_offer : "",
                brokerage_fee: [],
                date: "",
                status_id: null
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        // register option group
        if(options && options.option_groups) {
            console.log("option group method ran");
           this.addOptionGroups(options.option_groups);
        }

         // register future groups
        if(options && options.future_groups) {
            console.log("future group method ran");
           this.addFutureGroups(options.future_groups);
        }
    }


    getTradeStructures()
    {
        /* only care about the futuregroup the rest we do on the server */
        let options = [];
        this.future_groups.forEach((group)=>{
          options.push(group.prepareStore());
      });
        return options;
    }

    updateOptionColoumns(tradeStructureGroup)
    {
        console.log("update");
    }

    addOptionGroups(option_groups)
    {
        option_groups.forEach((group)=>{
            this.addOptionGroup(group);
        });
    }

    addOptionGroup(option_group)
    {
        if(!(option_group instanceof OptionGroup)) {
            option_group = new OptionGroup(option_group);
        } 

        this.option_groups.push(option_group);   
    }


    addFutureGroups(future_groups)
    {
        future_groups.forEach((group)=>{
            this.addFutureGroup(group);
        });
    }

    addFutureGroup(future_group)
    {
        if(!(future_group instanceof FutureGroup)) {
            future_group = new FutureGroup(future_group);
        } 
        this.future_groups.push(future_group);   
    }

  
    postPhaseTwo()
    {
        return new Promise((resolve, reject) => {
           axios.post(axios.defaults.baseUrl + '/trade/trade-confirmation/'+ this.id+'/phase-two', this.prepareStore())
           .then(response => {

            this.update(response.data.trade_confirmation);
            console.log(this);
            resolve();
        })
           .catch(err => {
            console.log(err);
            reject(new Errors(err.response.data));
        }); 
       });
    }

    send(trading_account)
    {
      return new Promise((resolve, reject) => {
           axios.put(axios.defaults.baseUrl + '/trade/trade-confirmation/'+ this.id,{
            "trading_account_id":trading_account.id,
            "trade_confirmation": this.prepareStore()
           })
           .then(response => {

            this.update(response.data.trade_confirmation);
            resolve();
        })
           .catch(err => {
            console.log(err);
            reject(new Errors(err.response.data));
        }); 
       });
    }

    confirm(trading_account)
    {
      return new Promise((resolve, reject) => {
           axios.post(axios.defaults.baseUrl + '/trade/trade-confirmation/'+ this.id+'confirm',{
            "trading_account_id":trading_account.id,
            "trade_confirmation": this.prepareStore()
           })
           .then(response => {

            this.update(response.data.trade_confirmation);
            resolve();
        })
           .catch(err => {
            console.log(err);
            reject(new Errors(err.response.data));
        }); 
       });
    }

    prepareStore() {
        return {
            id: this.id,
            structure_groups: this.getTradeStructures()
        };
    }
}
