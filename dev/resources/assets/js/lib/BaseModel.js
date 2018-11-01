
export default class BaseModel {

    constructor(options) {
        const defaults = {
            _used_model_list: [],
            _relations:{}
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
        Object.keys(this).forEach( key => {
            //changed from null to undifined check
            if(key[0] != '_' && typeof update_object[key] !== "undefined") {
                if(Array.isArray(update_object[key])) {
                    

                    //call array rebind method
                    this._updateArray(update_object[key],key);
                } else if (update_object[key] instanceof Object) {
                    

                    //call object rebind method
                    this._updateObject(update_object[key],key);
                } else {
                    if(this[key] instanceof moment) {

                        this[key] = moment(update_object[key]);
                    } else {
                        
                        this[key] = update_object[key];
                    }
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


       
       //@todo not entirly sure why ther's a check on the first model in array cause doesnt work on an empty array

       //this._isModelInstance(this[key][0]).is_model
        if(typeof this._relations[key] != 'undefined' && typeof this._relations[key].addMethod != 'undefined') {

            // loop to add or update new array objects
            for (let i = 0; i < update_arr.length; i++) {
                

                let index = this[key].findIndex((element) => {
                    return element.id == update_arr[i].id;
                });

                if(index == -1){
                    console.log("add method");
                    
                    this._relations[key].addMethod(update_arr[i]);
                    //this[key].push(update_arr[i]);    
                                        
                } else {
                    console.log("put method");

                    this[key][index].update(update_arr[i]);
                }
            }


            // loop to remove old objects
            for (let i = 0; i < this[key].length; i++) {


            if(key == "quotes")
            {

                let countTemp = update_arr.findIndex((element) => {
                    return element.id == this[key][i].id;
                });
               console.log('quote',this[key],countTemp); 

            }
            
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
            if(typeof this._relations[key] != 'undefined' && typeof this._relations[key].setMethod != 'undefined') {
                this._relations[key].setMethod(update_arr);
            } else {
                this[key] = update_arr;
            }
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
        if(this._isModelInstance(this[key]).is_model) {
            this[key].update(update_obj);
        } else if(typeof this._relations[key] != 'undefined' && typeof this._relations[key].setMethod != 'undefined') {
            this._relations[key].setMethod(update_obj);
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
        this._used_model_list.forEach( (elem) => {
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
        if(arr_to_validate.length == 0) {
            return true;
        }
        //check if all array values are the same instance else throw exception
        let is_valid = !!arr_to_validate.reduce( (acc, current) => { 
            let custom_check = this._isModelInstance(acc);
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