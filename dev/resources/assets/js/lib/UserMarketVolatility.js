import BaseModel from './BaseModel';
import UserMarket from './UserMarket';

export default class UserMarketVolatility extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: [],
            _relations:{}
        });

        const defaults = {
            id: null,
            group_id: null,
            value: null,
        };

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

        this._user_market = null;
    }

    /**
    *   setUserMarket - Sets the negotiations UserMarket
    *   @param {UserMarket} market - UserMarket for the negotiation
    */
    setUserMarket(user_market) {
        this._user_market = user_market;
    }

    prepareStore() {
        return {
            group_id: this.group_id,
            value: this.value,
        };
    }

    /**
    *   user_market Getter & Setter for parent relation
    */
    set user_market(user_market) {
        return this.setUserMarket(user_market);
    }
    get user_market() {
        return this._user_market;
    }
}