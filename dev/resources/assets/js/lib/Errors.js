module.exports =  class Errors {
    /**
     * Create a new Errors instance.
     */
    constructor(errors) {
        if(errors && errors.constructor == String) {
            this.errors = [];
            this.message = message;
        } else {
            // @TODO: test if axios

            // @TODO: handle if not axios
        }
    }


    /**
     * Determine if an errors exists for the given field.
     *
     * @param {string} field
     */
    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    /**
     * Determine if an errors exists for the given field.
     *
     * @param {string} field
     */
    state(field) {
        return this.errors.hasOwnProperty(field) ? "invalid" : null;
    }


    /**
     * Determine if we have any errors.
     */
    any() {
        return Object.keys(this.errors).length > 0;
    }

    all() {
        return Object.keys(this.errors);
    }


    list(unique) {
        unique = typeof unique === 'undefined' ? false : !!unique;
        return Object.values(this.errors).reduce((out, item) => {
            item.forEach(err => {
                if(unique) {
                    if(out.indexOf(err) == -1) {
                        out.push(err);
                    }
                } else {
                    out.push(err);
                }
            })
            return out;
        }, []);
    }


    /**
     * Retrieve the error message for a field.
     *
     * @param {string} field
     */
    get(field) {
        if (this.errors[field]) {
            return this.errors[field][0];
        }
    }


    /**
     * Record the new errors.
     *
     * @param {object} errors
     */
    record(errors) {
        this.errors = errors;
    }


    /**
     * Clear one or all error fields.
     *
     * @param {string|null} field
     */
    clear(field) {
        if (field) {
            delete this.errors[field];

            return;
        }

        this.errors = {};
    }
}