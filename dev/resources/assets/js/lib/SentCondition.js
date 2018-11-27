import BaseModel from './BaseModel';
import UserMarket from './UserMarket';
import UserMarketNegotiation from './UserMarketNegotiation';

export default class SentCondition extends BaseModel  {

    constructor(user_market, options) {
        
        super({
            _used_model_list: [UserMarketNegotiation],
        });

        const defaults = {
            type: null
        };
        this.condition = null;
        this.history = [];

        this._user_market = null;

        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        // has to be there
        if(user_market && user_market instanceof UserMarket) {
            this.user_market = user_market;
        } else {
            throw Error("SentCondition Missing UserMarket in construct");
        }

        if(options && options.condition) {
            this.setCondition(options.condition);
        }

        if(options && options.history) {
            this.setHistory(options.history);
        }

    }

    /**
    *   setCondition - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    setCondition(condition) {
        if(!(condition instanceof UserMarketNegotiation)) {
            condition = new UserMarketNegotiation(condition);
        }
        condition.setUserMarket(this._user_market);
        condition.setSentCondition(this);
        this.condition = condition;
    }

    /**
    *   addSentCondition - add user user_market_negotiation
    *   @param {UserMarketNegotiation} user_market_negotiation - UserMarketNegotiation objects
    */
    setHistory(history) {
        this.history.splice(0,this.history.length);
        history.forEach(negotiation => {
            if(!(negotiation instanceof UserMarketNegotiation)) {
                negotiation = new UserMarketNegotiation(negotiation);
            }
            negotiation.setUserMarket(this._user_market);
            negotiation.setSentCondition(this);
            this.history.push(negotiation);
        });
    }

    /**
    *   setUserMarket - Sets the negotiations UserMarket
    *   @param {UserMarket} market - UserMarket for the negotiation
    */
    set user_market(user_market) {
        this._user_market = user_market;
    }

    /**
    *   getUserMarket - Gets the negotiations UserMarket
    *   @return {UserMarket}
    */
    get user_market() {
        return this._user_market;
    }

}