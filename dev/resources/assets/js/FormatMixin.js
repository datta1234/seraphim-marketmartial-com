export default {
    methods: {
        /**
         * Takes in a value and formats it according to it's size to a currency format
         *
         * @param {string|number} val - the desired value to be formatted
         *
         * @return {string} the formated currency value
         */
        formatRandQty(val) {
            let sbl = "R";
            let calcVal = ( typeof val === 'number' ? val : parseFloat(val) );
            calcVal = calcVal % 1 != 0 ? calcVal.toFixed(2) : calcVal;
            //currently they want the format the same for all values
            switch(Math.ceil( ('' + Math.trunc(val)).length / 3)) {
                case 3: // 1 000 000 < x
                    //return sbl+(calcVal/1000000)+"m";
                break;
                case 2: // 1000 < x < 1 000 000
                    //return sbl + splitValHelper( calcVal, ' ', 3);
                case 1: // 100 < x < 1000
                case 0: // x < 100
                default:
                    return sbl + this.splitValHelper( calcVal, ' ', 3);
            }
        },
        /**
         * Takes in a value and splits the value by a splitter in a desired frequency
         *
         * @param {string|number} val - the desired value to split
         * @param {string} splitter - the splitter to split the value by
         * @param {number} frequency - the frequency in which to apply the split to the value
         *
         * @return {string} the newly splitted value
         */
        splitValHelper (val, splitter, frequency) {
            let tempVal = ('' + val);
            let floatVal = '';
            let sign = '';
            //Check if our passed value is negative signed
            if( ("" + val).indexOf('-') !== -1 ) 
            {
                sign = tempVal.slice(0,tempVal.indexOf('-') + 1);
                tempVal = tempVal.slice(tempVal.indexOf('-') + 1);
            }
            //Check if our passed value is a float
            if( ("" + tempVal).indexOf('.') !== -1 ) 
            {
                floatVal = tempVal.slice(tempVal.indexOf('.'));
                tempVal = tempVal.slice(0,tempVal.indexOf('.'));
            }
            //Creates an array of chars reverses and itterates through it
            return sign + tempVal.split('').reverse().reduce(function(x,y) {
                //adds a space on the spesified frequency position
                if(x[x.length-1].length == frequency)
                {
                   x.push("");
                }
                x[x.length-1] = y+x[x.length-1];
                return x;
            //Concats the array to a string back in the correct order
            }, [""]).reverse().join(splitter) + floatVal;
        },
        /*
         * Basic bubble sort that sorts a date string array usesing Moment.
         *
         * @param {String[]} date_string_array - array of date string
         * @param {String} format - the format to cast to a moment object
         */
        dateStringArraySort(date_string_array, format) {
            for(let i = 0; i < date_string_array.length - 1; i++) {
                for(let j = 0; j < date_string_array.length - i - 1; j++) {
                    if( moment(date_string_array[j+1],format).isBefore(moment(date_string_array[j],format)) ) {
                        let temp = date_string_array[j];
                        date_string_array[j] = date_string_array[j+1];
                        date_string_array[j+1] = temp;
                    }
                }
            }
        },
    }
};