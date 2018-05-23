module.exports = class UserMarket {

    constructor(options) {
        const defaults = {
            date: "",
            strike: "",
            bid: "",
            offer: "",
            state: ''
        }
        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && options[key]) {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });
    }

}