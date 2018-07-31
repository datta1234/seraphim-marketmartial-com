export default class BaseModel {

    constructor(options) {
        const defaults = {
            used_model_list: []
        }

        // assign options with defaults
        Object.keys(defaults).forEach(key => {
            if(options && typeof options[key] !== 'undefined') {
                this[key] = options[key];
            } else {
                this[key] = defaults[key];
            }
        });
    }

    /**
     *   update - updates the properties of this class and all classes that are assigned as properties
     *
     *   @param {Object} update_object - Object of type Class extended from BaseModel
     */
    update(update_object) {
        Object.keys(update_object).forEach(key => {
            if(key[0] != '_' && update_object[key] != null) {
                if(Array.isArray(value)) {
                    //call array rebind method
                    this._updateArray(update_object[key],key);
                } else if (value instanceof Object) {
                    //call object rebind method
                    this._updateObject(update_object[key],key);
                } else {
                    this[key] = update_object[key];
                }    
            }
        });
    }

    /**
     *   _updateArray - Updates a class array propery with an array param.
     *                  Check whether the array values are of the same type then reassigns the
     *                  array to update or loops through a class model instances property and 
     *                  updates accordingly.
     *
     *   @param {Array} update_arr - An array of values to update the corrosponding class property
     *   @param {string} key - a string key corrosponding to the class property
     */
    _updateArray(update_arr, key) {
        // test array items are all of the same instance 
        this._validateArray(update_arr);

        // check list of Models if one call update 
        if(_isModelInstance(update_arr[0]).is_model) {
            
            // loop to add or update new array objects
            for (let i = 0; i < update_arr.length; i++) {
                let index = this[key].findIndex((element) => {
                    return element.id == update_arr[i].id;
                });
                if(index == -1){
                    this[key].push(update_arr[i]);
                } else {
                    this[key][index].update(update_arr[i]);
                }
            }

            // loop to remove old objects
            for (let i = 0; i < this[key].length; i++) {
                let index = update_arr.findIndex((element) => {
                    return element.id == this[key][i].id;
                });
                if(index == -1) {
                    this[key].splice(i,1);
                    i--;
                }
            }
        // update array (check if we can just assign or need to pop and push all new ones)
        } else {
            this[key] = user_market_request[key];
        }
    }

    /**
     *   _updateObject - Updates a class object propery with an object param.
     *                   Check whether the object is a class model instances property type and
     *                   calls it's update method or assigns the update object to class property.
     *
     *   @param {Array} update_arr - An array of values to update the corrosponding class property
     *   @param {string} key - a string key corrosponding to the class property
     */
    _updateObject(update_obj, key) {

        if(_isModelInstance(update_obj).is_model) {
            this[key].update(update_obj);
        } else {
            if( !(typeof this[key] == 'undefined') && !(this[key] == null) && !(typeof update_obj == 'undefined') && !(update_obj == null)) {
                Object.assign(this[key], update_obj);
            }
        }
    }

    /**
     *   _isModelInstance - Checks if the passed param is an instance of one of the class's models 
     *
     *   @param {Any} check_elem - a passed element to evaluate
     *
     *   @returns {Object} - returns an object containing a boolean and an Object instance / null
     */
    _isModelInstance(check_elem) {
        let elem_state = {is_model:false, instance_of:null};
        this.used_model_list.forEach( (elem) => {
            if (check_elem instanceof elem) {
                elem_state.is_model = true;
                elem_state.instance_of = elem;
            }
        });

        return elem_state;
    }

    /**
     *   _validateArray - Checks if the passed param is an array of a single type and throws an exception
     *                    if it is not.
     *
     *   @param {Array|Any} arr_to_validate - a passed array to evaluate
     *
     *   @throws "The array values are not of a uniform type"
     */
    _validateArray(arr_to_validate) {
        //check if all array values are the same instance else trow exception
        let is_valid = !!arr_to_validate.reduce( (acc, current) => { 
            let custom_check = isModelInstance(acc);
            if(custom_check.is_model) {
                return (current instanceof custom_check.instance_of) ? acc : NaN;
            }

            return (typeof acc == typeof current) ? acc : NaN;
        });

        if(!is_valid) {
            throw "The array values are not of a uniform type";
        }
    }

    /**
     * toJSON - override removing internal references
     */
    toJSON() {
        let json = {};
        Object.keys(this).forEach(key => {
            if(key[0] != '_') {
                json[key] = this[key];
            }
        });
        return json;
    }
}