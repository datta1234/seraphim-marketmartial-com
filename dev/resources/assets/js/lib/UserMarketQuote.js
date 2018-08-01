import BaseModel from './BaseModel';

export default class UserMarketQuote extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: []
        });

        // default internal
        this._user_market_request = null;
        // default public
        const defaults = {
            id: "",
            is_maker: false,
            is_interest: false,
		    bid_only: false,
		    offer_only: false,
		    vol_spread: null,
		    time: "",

            bid_qty: null,
            bid: null,
            offer: null,
            offer_qty: null,
            is_on_hold: false
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });
    }

    /**
    *   setParent - Set the parent UserMarketRequest
    *   @param {UserMarketRequest} user_market_request - UserMarketRequest object
    */
    setMarketRequest(user_market_request) {
        this._user_market_request = user_market_request;
    }

    /**
    *   getParent - Get the parent UserMarketRequest
    *   @return {UserMarketRequest}
    */
    getMarketRequest() {
        return this._user_market_request;
    }

    putOnHold() {
        console.log('putting on hold now :D');
        // catch not assigned to a user market request yet!
        if(this._user_market_request == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market Request"]));
            });
        }
        
        return axios.patch(axios.defaults.baseUrl + '/trade/user-market-request/'+this._user_market_request.id+'/user-market/'+this.id, {'is_on_hold': true})
        .then(response => {
            console.log("Putting Quote on Hold response: ", response);
            return response;
        })
        .catch(err => {
            return new Errors(err);
        }); 
    }

    /**
    *   update - updates this User Market Quote
    *   @param {UserMarketQuote} user_market_quote - UserMarketQuote object
    */
    update(user_market_quote) {
        if(user_market_quote !== null){
            Object.entries(user_market_quote).forEach( ([key, value]) => {
                if(value !== null){
                    if(key == "_user_market_request") {
                        this.setMarketRequest(value);
                    } else {
                        this[key] = value;
                    }
                }
            });
        }
    }
}