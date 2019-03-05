import BaseModel from './BaseModel';
import UserMarketRequest from './UserMarketRequest';
import { EventBus } from './EventBus';

export default class Market extends BaseModel {

    constructor(options) {
        super({
            _used_model_list: [UserMarketRequest]
        });

        this.market_requests = [];
        this.children = [];
        this._parent = null;
        const defaults = {
            id: "",
            title: "",
            description: "",
            order: "",
            market_type_id: "",
            parent_id: "",
            is_displayed: true,
            is_seldom: false,
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });

        // register markets
        if(options && options.market_requests) {
            this.addMarketRequests(options.market_requests);
        }

        // register market children
        if(options && options.children) {
            this.addChildren(options.children);
        }
    }

    is(type) {
        switch(type) {
            case 'index':
                return this.title.toUpperCase() == 'INDEX';
            break;
            case 'singles':
                return this.title.toUpperCase() == 'SINGLES';
            break;
            // add ass you need
        }
    }

    /**
    *   addMarketRequest - add user market
    *   @param {UserMarketRequest} market - UserMarket objects
    */
    addMarketRequest(market_req) {
        let is_new_market = true;
        if(!(market_req instanceof UserMarketRequest)) {
            market_req = new UserMarketRequest(market_req);
        }
        for (let i = 0; i < this.market_requests.length; i++) {
            if (this.market_requests[i].id === market_req.id) {
                is_new_market = false;
            }
        }
        if (is_new_market) {
            market_req.setMarket(this);
            this.market_requests.push(market_req);
            EventBus.$emit('force-display-update');
        } else {
        }
    }

    /**
    *   updateMarketRequest - updates a current user market
    *   @param {UserMarketRequest} market_req - UserMarket objects
    *   @param {Int} index - UserMarket objects
    *   
    *   @todo make general Market update method that updates all market requests
    */
    updateMarketRequest(market_req, index) {
        // @TODO fix updating root property before we enable this again.
        this.market_requests[index].update(market_req);
    }

    /**
    *   addMarketRequests - add array of user market_requests
    *   @param {Array} market_requests - array of UserMarketRequest objects
    */
    addMarketRequests(market_requests) {
        console.log("Adding ["+market_requests.length+"] Market Requests");
        market_requests.forEach(market_req => {
            this.addMarketRequest(market_req);
        });
    }

    /**
    *   addChild - add user market
    *   @param {UserMarketRequest} market - UserMarket objects
    */
    addChild(market) {
        market.setParent(this);
        this.children.push(market);
    }

    /**
    *   addChildren - add array of user market_requests
    *   @param {Array} market_requests - array of UserChild objects
    */
    addChildren(markets) {
        markets.forEach(market => {
            if(!(market instanceof Market)) {
                market = new Market(market);
            }
            this.addChild(market);
        });
    }

    /**
    *   setParent - Set the parent Market
    *   @param {Market} market - Market object
    */
    setParent(market) {
        this._parent = market;
    }

    /**
    *   getParent - Get the parent Market
    *   @return {Market}
    */
    getParent() {
        return this._parent;
    }
}