export default class RequestGroup {

    constructor(structureGroup) {
        this.setByStructure(structureGroup);
    }

    update(structureGroup)
    {
        this.setByStructure(structureGroup);
    }

    setByStructure(structureGroup)
    {
        structureGroup.items.forEach(item => {
            switch(item.title) {
                case 'Expiration Date':
                    this.expires_at = this.setUpDate(item.value);
                    break;
                case 'Quantity':
                    this.vega = this.setUpNumbers(item.value);
                    break;
                case 'Cap':
                    this.cap = this.setUpNumbers(item.value);
                    break;
                case 'Future':
                    this.future = this.setUpNumbers(item.value);
                    break;
            }
        });

        this.underlying_title = structureGroup.tradable ? structureGroup.tradable.title: null;
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

