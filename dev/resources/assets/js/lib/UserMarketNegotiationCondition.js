import BaseModel from './BaseModel';

export default class UserMarketNegotiationCondition extends BaseModel {

    constructor(options) {
        super({
            used_model_list: []
        });

        // default internal
        this._user_market_negotiation = null;
        // default public
        const defaults = {
            id: "",
            title: "",
            alias: "",
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
    }

    /**
    *   setUserMarketNegotiation - Sets the conditions UserMarketNegotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation for the condition
    */
    setUserMarketNegotiation(user_market_negotiation) {
        this._user_market_negotiation = user_market_negotiation;
    }

    /**
    *   getUserMarketNegotiation - Gets the conditions  UserMarketNegotiation
    *   @return {UserMarketNegotiation}
    */
    getUserMarketNegotiation() {
        return this._user_market_negotiation;
    }

    prepareStore() {
        return {
            id: this.id,
            title: this.title,
            alias: this.alias,
        };
    }

}