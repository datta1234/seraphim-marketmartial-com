module.exports = class DerivativeMarket {

    constructor(options) {
        const defaults = {
            title: "",
            markets: []
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