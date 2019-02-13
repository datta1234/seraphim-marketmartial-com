import BaseModel from './BaseModel';

import OptionGroup from './tradeconfirmations/OptionGroup';
import FutureGroup from './tradeconfirmations/FutureGroup';
import RequestGroup from './tradeconfirmations/RequestGroup';
import Errors from './Errors';
import util from './util';

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
                    setMethod: (future_groups) => { 
                        this.setFutureGroups(future_groups) 
                    },
                },
                // request groups - only used for var_swap
                request_groups: {
                    addMethod: (request_groups) => { 
                        this.addRequestGroups(request_groups) 
                    }
                }
            }
        })

        this.option_groups = [];
        this.future_groups = [];
        this.request_groups = [];

        const defaults = {
                id : "",
                root_id : "",
                organisation : "",
                trade_structure_title : "",
                volatility : "",
                structure_groups : [],
                market_id : "",
                market_type_id : "",
                market_request_id : "",
                market_request_title : "",
                underlying_title : "",
                underlying_id : "",
                is_single_stock : "",
                traded_at : "",
                is_offer : "",
                brokerage_fee: [],
                date: "",
                state: null,
                swap_parties: null,
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
           this.addOptionGroups(options.option_groups);
        }

        // register future groups
        if(options && options.future_groups) {
           this.addFutureGroups(options.future_groups);
        }

        // register request groups
        if(options && options.request_groups) {
           this.addRequestGroups(options.request_groups);
        }
    }


    getTradeStructures(exclude_list)
    {
        /* only care about the futuregroup the rest we do on the server */
        let options = [];
        this.future_groups.forEach((group)=>{
          options.push(group.prepareStore(exclude_list));
        });
        return options;
    }

    get trade_structure_slug() {
        return util.resolveTradeStructureSlug(this.trade_structure_title);
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

    addRequestGroups(request_groups)
    {
        request_groups.forEach((group)=>{
            this.addRequestGroup(group);
        });
    }

    addRequestGroup(request_group)
    {
        if(!(request_group instanceof RequestGroup)) {
            request_group = new RequestGroup(request_group);
        } 
        this.request_groups.push(request_group);   
    }
  
    postPhaseTwo(trading_account)
    {
        console.log("calculate");
        if(!trading_account) {
            return new Promise((resolve, reject) => {
                reject({errors:{'trading_account_id':["Trading account is required"]}});
            });
        }
        return new Promise((resolve, reject) => {
           axios.post(axios.defaults.baseUrl + '/trade/trade-confirmation/'+ this.id+'/phase-two',{
            "trading_account_id":trading_account.id,
            "trade_confirmation_data": this.prepareStore()
           })
           .then(response => {
            // this.update(response.data.data);
            resolve(response);
        })
           .catch(err => {
            reject(err);
        }); 
       });
    }

    send(trading_account)
    {
        return axios.put(axios.defaults.baseUrl + '/trade/trade-confirmation/'+ this.id,{
            "trading_account_id":trading_account.id,
            "trade_confirmation_data": this.prepareStore()
        })
        .then(response => {
            this.update(response);
            return response;
        }); 
    }

    confirm(trading_account)
    {
      return new Promise((resolve, reject) => {
           axios.post(axios.defaults.baseUrl + '/trade/trade-confirmation/'+ this.id+'/confirm',{
            "trading_account_id":trading_account.id,
            "trade_confirmation": this.prepareStore()
           })
           .then(response => {
            resolve(response);
        })
           .catch(err => {
            reject(err);
        }); 
       });
    }

    dispute(trading_account)
    {
        return axios.post(axios.defaults.baseUrl + '/trade/trade-confirmation/'+ this.id+'/dispute',{
            "trading_account_id":trading_account.id,
            "trade_confirmation": this.prepareStore()
        })
        .then(response => {
            this.update(response);
            return response;
        });
    }

    hasFutures()
    {
        return this.future_groups.reduce((out, group)=>{
                if( group.hasOwnProperty('future') && group.future == null )
                {
                    out = false;
                }

                if( group.hasOwnProperty('future_1') && group.future_1 == null )
                {
                    out = false;
                }
                return out;
            }, true);

    }

    /**
     * Validation method that checks if this TradeConfirmation has a spot in it's future groups
     *   and it contains a value
     *
     * @return {Boolean}
     */
    hasSpots()
    {
        return this.future_groups.reduce( (out, group) => {
                if( group.hasOwnProperty('spot') && group.spot == null )
                {
                    out = false;
                }
                return out;
            }, true);

    }

    /**
     *   canDisputeUpdated - Checks to see if the user can dispute an updated TradeConfirmation on 
     *      the current state of the TradeConfirmation
     *
     *   @return {Boolean}
     */
    canDisputeUpdated()
    {
        let disputableStatuses = [
            "Updated By Sender",
            "Updated By Reciever",
        ];
            
        return  disputableStatuses.indexOf(this.state) > -1;
    }

    /**
     *   canDisputeContracts - Checks to see if the user can dispute a TradeConfirmation on the current
     *      state of the TradeConfirmation if no update has been done.
     *
     *   @return {Boolean}
     */
    canDisputeContracts() {
        let disputableStatuses = [
            "Pending: Reciever Confirmation",
            "Disputed: By Reciever",
            "Disputed: By Sender",
        ];

        return  disputableStatuses.indexOf(this.state) > -1;
    }

    /**
     *   canSend - Checks to see if the user can send a TradeConfirmation on the current state of the TradeConfirmation
     *
     *   @return {Boolean}
     */
    canSend()
    {
        let disputableStatuses = [
            "Pending: Initiate Confirmation",
            "Pending: Reciever Confirmation",
            "Disputed: By Reciever",
            "Disputed: By Sender",
        ];
            
        return  disputableStatuses.indexOf(this.state) > -1;
    }

    /**
     *   hasChanged - Checks to see if a TradeConfirmation's FutureGroups have changed and excludes an array of 
     *      passed future group items to ignore in the check
     *
     *   @return {Boolean}
     */
    hasChanged(oldConfirmationData, exclude_list) {
        if(!Array.isArray(exclude_list)) {
            exclude_list = [];
        }
        
        if(this.state == 'Pending: Initiate Confirmation' && exclude_list.indexOf('contracts') == -1) {
            exclude_list.push('contracts');
        }

        return JSON.stringify(oldConfirmationData) != JSON.stringify(this.prepareStore(exclude_list));
    }

    /**
     * Method that returns store object of this TradeConfirmation
     * 
     * @param {Array} exclude_list - An array of properties to ignore for comparison purposes
     *
     * @return {Boolean}
     */
    prepareStore(exclude_list) {
        return {
            id: this.id,
            structure_groups: this.getTradeStructures(exclude_list)
        };
    }


}
