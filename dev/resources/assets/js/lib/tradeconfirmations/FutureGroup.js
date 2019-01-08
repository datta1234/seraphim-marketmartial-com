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
        console.log("this is what I am looping through: ", structureGroup);   
        structureGroup.trade_confirmation_items.forEach(item => {
            switch(item.title) {
                case 'is_offer':
                    this.is_offer = item.value == 1;
                    break;
                case 'Expiration Date':
                    this.expires_at = this.setUpDate(item.value);
                    break;
                case 'Contract':
                    this.contracts = item.value;
                    break;
                case 'Future':
                    this.future = item.value != null ? parseFloat(item.value) : item.value;
                    break;
                case 'Spot':
                    this.spot = item.value != null ? parseFloat(item.value) : item.value;
                    break;
            }
        });
        
        /*this.is_offer = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }) ? structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'is_offer' }).value == 1 : null; 
        this.expires_at = structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }) ? this.setUpDate(structureGroup.user_market_request_group.items.find((item)=>{ return item.title == 'Expiration Date' }).value) : null;
        this.contracts = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Contract' }).value) : null;
        this.future = structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Future' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item)=>{ return item.title == 'Future' }).value) : null;
        //this.spot = structureGroup.trade_confirmation_items.find((item)=> { return item.title == 'Spot' }) ? parseFloat(structureGroup.trade_confirmation_items.find((item) => { return item.title == 'Spot' }).value) : null;
        this.underlying_title = structureGroup.user_market_request_group.tradable ? structureGroup.user_market_request_group.tradable.title: null; */

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
        let store_items = [];

        Object.keys(this).forEach(key => {
            switch(key) {
                case 'contracts':
                    store_items.push({
                        "title": "Contract",
                        "value": parseFloat(this.contracts)
                    });
                    break;
                case 'future':
                    store_items.push({
                        "title": "Future",
                        "value": parseFloat(this.future)
                    });
                    break;
                case 'spot':
                    store_items.push({
                        "title": "Spot",
                        "value": parseFloat(this.spot)
                    });
                    break;
            }
        });

        return {
            id: this.id,
            is_option: false,
            items: store_items,
        };
    }   
}

