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
        structureGroup.trade_confirmation_items.forEach(item => {
            switch(item.title) {
                case 'is_put':
                    this.is_put = item.value == 1;
                    break;
                case 'is_offer':
                    this.is_offer = item.value == 1;
                    break;
                case 'Expiration Date':
                    this.expires_at = this.setUpDate(item.value);
                    break;
                case 'Contract':
                    this.contracts = this.setUpNumbers(item.value);
                    break;
                case 'Nominal':
                    this.nominal = this.setUpNumbers(item.value);
                    break;
                case 'Volatility':
                    this.volatility = this.setUpNumbers(item.value);
                    break;
                case 'Strike':
                    this.strike = this.setUpNumbers(item.value);
                    break;
                case 'Gross Premiums':
                    this.gross_prem = this.setUpNumbers(item.value);
                    break;
                case 'Net Premiums':
                    this.net_prem = this.setUpNumbers(item.value);
                    break;
                
            }
        });

        structureGroup.user_market_request_group.items.forEach(item => {
            switch(item.title) {
                case 'Expiration Date':
                    this.expires_at = this.setUpDate(item.value);
                    break;
                case 'Strike':
                    this.strike = this.setUpNumbers(item.value);
                    break;
                case 'Quantity':
                    this.spot = this.setUpNumbers(item.value);
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

