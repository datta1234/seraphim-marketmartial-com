export default class FutureGroup {

    constructor(options) {

        const defaults = {
            is_offer:"",
            underlying_title:"",
            spot:null,
            future:null,
            expires_at:null,
            volatility:null,
            contracts:null,
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
        this.expires_at = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }) ? structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }).value : null; ;
        this.contracts = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'contract' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'contract' }).value) : null; ;

    }
}

