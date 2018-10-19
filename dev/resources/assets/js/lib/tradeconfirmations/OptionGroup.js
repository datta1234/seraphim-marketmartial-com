export default class OptionGroup {

    constructor(options) {

        const defaults = {
            is_offer:"",
            underlying_title:"",
            strike:"",
            put_call:"",
            nominal:"",
            quantity:"",
            expires_at:"",
            volatility:"",
            gross_premiums:"",
            net_premiums:"",
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
        this.volatility = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'volatility' }) ? structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'volatility' }).value : null;
        this.expires_at = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }) ? structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }).value : null; ;
        this.strike = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }) ? structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }).value : null; ;
        this.quantity = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Quantity' }) ? structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Quantity' }).value : null; ;

    }
}

