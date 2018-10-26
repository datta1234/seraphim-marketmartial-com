export default class OptionGroup {

    constructor(options) {

        const defaults = {
            is_offer:null,
            underlying_title:null,
            strike:null,
            put_call:null,
            nominal:null,
            contracts:null,
            expires_at:null,
            volatility:null,
            future:null,
            gross_prem:null,
            net_prem: null

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

    constructFromStructureGroup(structureGroup)
    {
        this.is_offer = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }) ? !!structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }).value : null; 
        this.volatility = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Volatility' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Volatility' }).value) : null;
        this.expires_at = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }) ? structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }).value : null; ;
        this.strike = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }) ? parseFloat(structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }).value) : null; ;
        this.contracts = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }).value) : null; ;

    }
}

