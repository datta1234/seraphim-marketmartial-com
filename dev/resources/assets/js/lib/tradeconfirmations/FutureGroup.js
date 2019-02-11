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
        structureGroup.trade_confirmation_items.forEach(item => {
            switch(item.title) {
                case 'is_offer':
                    this.is_offer = item.value == 1;
                    break;
                case 'is_offer 1':
                    this.is_offer_1 = item.value == 1;
                    break;
                case 'is_offer 2':
                    this.is_offer_2 = item.value == 1;
                    break;
                case 'Expiration Date':
                    this.expires_at = this.setUpDate(item.value);
                    break;
                case 'Contract':
                    this.contracts = this.setUpNumbers(item.value);
                    break;
                case 'Future':
                    this.future = this.setUpNumbers(item.value);
                    break;
                case 'Future 1':
                    this.future_1 = this.setUpNumbers(item.value);
                    break;
                case 'Future 2':
                    this.future_2 = this.setUpNumbers(item.value);
                    break;
                case 'Spot':
                    this.spot = this.setUpNumbers(item.value);
                    break;
            }
        });

        structureGroup.user_market_request_group.items.forEach(item => {
            switch(item.title) {
                case 'Expiration Date':
                    this.expires_at = this.setUpDate(item.value);
                    break;
                case 'Expiration Date 1':
                    this.expires_at_1 = this.setUpDate(item.value);
                    break;
                case 'Expiration Date 2':
                    this.expires_at_2 = this.setUpDate(item.value);
                    break;
                case 'Strike':
                    this.strike = this.setUpNumbers(item.value);
                    break;
            }
        });

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

    prepareStore(exclude_list) {
        if(!Array.isArray(exclude_list)) {
            exclude_list = [];
        }
        let store_items = [];

        Object.keys(this).forEach(key => {
            if(!exclude_list.includes(key)) {
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
                    case 'future_1':
                        store_items.push({
                            "title": "Future 1",
                            "value": parseFloat(this.future_1)
                        });
                        break;
                    case 'future_2':
                        store_items.push({
                            "title": "Future 2",
                            "value": parseFloat(this.future_2)
                        });
                        break;
                    case 'spot':
                        store_items.push({
                            "title": "Spot",
                            "value": parseFloat(this.spot)
                        });
                        break;
                }
            }
        });

        return {
            id: this.id,
            is_option: false,
            items: store_items,
        };
    }   
}

