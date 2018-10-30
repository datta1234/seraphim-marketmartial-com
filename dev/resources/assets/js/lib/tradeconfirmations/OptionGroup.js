export default class OptionGroup {

    constructor(structureGroup) {

        this.id = structureGroup.id;
        this.setByStructure(structureGroup);
    }

    update(structureGroup)
    {
        this.setByStructure(structureGroup);
    }

    setByStructure(structureGroup)
    {
        this.contracts = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }).value) : null;
        this.is_offer = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }) ? !!structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }).value : null; 
        this.volatility = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Volatility' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Volatility' }).value) : null;
        this.expires_at = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }) ? structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }).value : null;
        this.strike = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }) ? parseFloat(structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }).value) : null;
        this.gross_prem = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Gross Premiums' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Gross Premiums' }).value) : null;
        this.net_prem = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Net Premiums' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Net Premiums' }).value) : null;   
        this.underlying_title = structureGroup.user_market_request_group.tradable ? structureGroup.user_market_request_group.tradable.title: null; 
    }

   
}

