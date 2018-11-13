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
        this.is_put = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_put' }) ? structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_put' }).value == 1 : null; 
        this.contracts = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }) ? this.setUpNumbers(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }).value) : null;
        this.is_offer = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }) ? structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }).value == 1 : null; 
        this.volatility = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Volatility' }) ? this.setUpNumbers(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Volatility' }).value) : null;
        this.expires_at = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }) ? this.setUpDate(structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }).value) : null;
        this.strike = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }) ? this.setUpNumbers(structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Strike' }).value) : null;
        this.gross_prem = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Gross Premiums' }) ? this.setUpNumbers(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Gross Premiums' }).value) : null;
        this.net_prem = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Net Premiums' }) ? this.setUpNumbers(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Net Premiums' }).value) : null;   
        this.underlying_title = structureGroup.user_market_request_group.tradable ? structureGroup.user_market_request_group.tradable.title: null; 
    }

    setUpDate(value)
    {
        if(value == null)
        {
            return null;
        }else
        {
            return  moment(value,'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY');
        }
    }

    setUpNumbers(value)
    {
        if(value == null)
        {
            return null;
        }else
        {
            return parseFloat(value);
        }
    }
   
}

