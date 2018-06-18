import axios from 'axios';

this._data = {};
this._loaded = false;
this._load_path = "/config/trade_structures.json";

/**
*   parseAttributes function - returns a parsed representation of fields from a specific trade structure
*/
this.parseAttributes = (trade_structure, cb) => {
    console.log(trade_structure, this._data);
};

/**
*   load function - ajax loads the data for the object
*/
this.load = () => {
    this._loaded = true;
    // do ajax load
    return axios.get(this._load_path)
    .then(response => {
        if(response.status == 200) {
            this._data = response.data;
        } else {
            this._loaded = false;
            console.error(err);    
        }
    }, err => {
        this._loaded = false;
        console.error(err);
    });
}

/**
*   init function - runs load and returns the object
*/
this.init = () => {
    if(!this._loaded) {
        this.load();
    }
    return this;
}

export default this;