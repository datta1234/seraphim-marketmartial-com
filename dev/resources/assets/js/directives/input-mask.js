/**
*   v-input-mask directive
*   Usage:
*       <input v-input-mask.number.decimal="{ precision: 2 }">
*
*   Modifiers:
*       .alpha will only allow alphabetical values a-Z
*       .number will only allow numeric values 0-9
*       .decimal will allow decimal point '.'
*
*   Options:
*       precision: set the allowed decimal precission (length after decimal point)
*       negative: Allows negative values
*/
export default {
  bind (el, binding) {
    el.addEventListener('keydown', (e) => {
        // delete, backpsace, tab, escape, enter,
        let special = [46, 8, 9, 27, 13]
        if (binding.modifiers['decimal']) {
            // decimal(numpad), period
            special.push(110, 190)
        }
        // If negative add special for negatives
        if(
            binding.value 
            && binding.value['negative'] 
            && (e.keyCode === 109 || (e.keyCode === 189 && e.key == '-'))
        ) {
          return // allow 
        }
        // special from above
        if (
            special.indexOf(e.keyCode) !== -1 ||
            // Ctrl+A
            (e.keyCode === 65 && e.ctrlKey === true) ||
            // Ctrl+C
            (e.keyCode === 67 && e.ctrlKey === true) ||
            // Ctrl+X
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)
        ) {
            return // allow
        }
        if ((binding.modifiers['alpha']) &&
            // a-z/A-Z
            (e.keyCode >= 65 && e.keyCode <= 90)
        ) {
            return // allow
        }
        if ((binding.modifiers['number']) &&
            // number keys without shift
            ((!e.shiftKey && (e.keyCode >= 48 && e.keyCode <= 57)) ||
            // numpad number keys
            (e.keyCode >= 96 && e.keyCode <= 105))
        ) {
            // have a precission for decimals
            if(binding.value && typeof binding.value['precision'] !== 'undefined') {
                // has decimal yet
                if(el.value!=null && el.value.indexOf(".")>-1){
                    // restrict to 'n' decimal places
                    if(el.value.split('.')[1].length < binding.value['precision']) {
                        return // allow
                    }
                } else {
                    return // allow
                }
            }
            // no decimal precision set 
            else {
                return // allow
            }
        }
        // otherwise stop the keystroke
        e.preventDefault() // prevent
    }) // end addEventListener
  }, // end bind
  update: (el, binding, vnode, oldVnode) => {
    // Checks instances of a negative in the value and add's it to the front or removes it if toggled again
    if(
      binding.value 
      && binding.value['negative']
      && binding.value['negative_callback']
      && vnode.data.model 
      && typeof vnode.data.model.value === 'string'
    ) {
      let result;
      if(vnode.data.model.value == '--' || vnode.data.model.value == '-') {
        // Deals with Case where no numbers have been added
        console.log(vnode.data.model.value.length);
        result = vnode.data.model.value.length > 1 ? '' : vnode.data.model.value;
      } else {
        let split_val = vnode.data.model.value.split('-');
        // Toggle negatives for the value
        result = (split_val.length - 1) % 2 == 0 ? split_val.join('') : ['-'].concat(split_val).join('');
      }

      binding.value['negative_callback'](result);
    }
  }
}