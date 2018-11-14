export default class FutureGroup {

    constructor(structureGroup) {

        this.id = structureGroup.id;
        this.setBystructureGroup(structureGroup);
    }

    update(structureGroup)
    {
        this.setBystructureGroup(structureGroup);
    }

    setBystructureGroup(structureGroup)
    {
        this.is_offer = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }) ? structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }).value == 1 : null; 
        this.expires_at = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }) ? this.setUpDate(structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }).value) : null; ;
        this.contracts = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }).value) : null; ;
        this.future = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Future' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Future' }).value) : null; ;
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

    prepareStore() {
        return {
            id: this.id,
            is_option: false,
            items: [
                {
                    "title": "Future",
                    "value": this.future
                },
                {
                    "title": "Contract",
                    "value": this.contracts
                }
            ]
        };
    }   
}

