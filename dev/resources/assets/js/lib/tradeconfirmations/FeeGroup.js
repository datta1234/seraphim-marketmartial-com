export default class FeeGroup {

    constructor(structureGroup) {

        this.id = structureGroup.id;
        this.setByStructureGroup(structureGroup);
    }

    update(structureGroup)
    {
        this.setByStructureGroup(structureGroup);
    }

    setByStructureGroup(structureGroup)
    {
        structureGroup.trade_confirmation_items.forEach(item => {
            switch(item.title) {
                case 'Fee Total':
                    this.fee_total = this.setUpNumbers(item.value);
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
}

