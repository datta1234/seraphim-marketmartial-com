export default class UserMarketNegotiation {

    constructor(options) {
        // default internal
        this._user_market = null;
        // default public
        this.conditions = [];
        const defaults = {
            id: "",
            bid: "",
            offer: "",
            bid_qty: 500,
            offer_qty: 500,
            is_repeat: false,
            has_premium_calc: false,
            bid_premium: "",
            offer_premium: "",
            is_put: false,
            status: "",
            created_at: moment(),
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
                if(defaults[key] instanceof moment) {
                    this[key] = moment(this[key]);
                }
            } else {
                this[key] = defaults[key];
            }
        });

        // register conditions
        if(options && options.user_market_negotiation_condition) {
            this.addUserMarketNegotiationConditions(options.user_market_negotiation_condition);
        }
    }

    /**
    *   setUserMarket - Sets the negotiations UserMarket
    *   @param {UserMarket} market - UserMarket for the negotiation
    */
    setUserMarket(user_market) {
        this._user_market = user_market;
    }

    /**
    *   getUserMarket - Gets the negotiations UserMarket
    *   @return {UserMarket}
    */
    getUserMarket() {
        return this._user_market;
    }

    /**
    *   addNegotiation - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    addUserMarketNegotiationCondition(user_market_negotiation_condition) {
        user_market_negotiation_condition.setUserMarketNegotiation(this);
        this.conditions.push(user_market_negotiation_condition);
    }

    /**
    *   addNegotiations - add array of user market_negotiations
    *   @param {Array} market_negotiations - array of UserMarketNegotiation objects
    */
    addUserMarketNegotiationConditions(user_market_negotiation_conditions) {
        user_market_negotiation_conditions.forEach(user_market_negotiation_condition => {
            this.addUserMarketNegotiationCondition(user_market_negotiation_condition);
        });
    }

    /**
    * toJSON - override removing internal references
    */
    toJSON() {
        let json = {};
        Object.keys(this).forEach(key => {
            if(key[0] != '_') {
                json[key] = this[key];
            }
        });
        return json;
    }

    /**
    *   patch - server side patch the negotiation with the ne input
    *   
    */
    patch() {
        // catch not assigned to a market request yet!
        if(this.user_market_request_id == null) {
            return new Promise((resolve, reject) => {
                reject(new Errors(["Invalid Market Request"]));
            });
        }

        return axios.post(axios.defaults.baseUrl + "/trade/market-request/"+this.user_market_request_id+"/user-market/"+this.getUserMarket().id+"/user-market-negotiation/"+this.id, this.prepareStore())
        .then(response => {
            console.log(response);
            return response;
        })
        .catch(err => {
            console.error(err);
            return new Errors(err);
        });
    }


    prepareStore() {
        return {
            bid: this.bid,
            offer: this.offer,
            bid_qty: this.bid_qty,
            offer_qty: this.offer_qty,
            is_repeat: this.is_repeat,
            has_premium_calc: this.has_premium_calc,
            bid_premium: this.bid_premium,
            offer_premium: this.offer_premium,
            conditions: this.conditions.map(x => x.prepareStore()),
        };
    }
}