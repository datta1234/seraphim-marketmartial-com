<?php

namespace App\Traits;

/** 
    Add the following to the model applying conditions, then add the applicable conditions
    
        protected $applicableConditions = [
            'attribute_name' => 'default_value'
        ];

    Conditons are applied based on a method name pattern of "apply<_ConditonNameInCamelCase_>Condtion"
    eg: applyMyThingCondition(){ ... }

    Add the following boolean attribute to the model applying conditions, and toggle to skip an endless loop
    when updating model post save.
    
        public $conditions_applied = false;
*/
trait AppliesConditions {
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function bootAppliesConditions()
    {
        static::saving(function($item) {
            /*
             *  Used for after post timeout condition a Job is fired and the Job id is saved
             *  causing function to be called again. Avoids re-running methods.
             */
            if(property_exists($item,'conditions_is_applied') && $item->conditions_is_applied) {
                return;
            }
            if($item->applicableConditions) {
                // go through all registered condition attributes
                foreach($item->applicableConditions as $attr => $default) {

                    /* 
                    *   Only apply if:
                    *       1) The attribute is not the default value
                    *       2) The attribute has been changed (ie: dirty)
                    */
                    if($item->$attr !== $default && $item->isDirty($attr)) {
                        if(method_exists($item, camel_case('apply_'.$attr.'_condition'))) {
                            // method exists, lets apply the condition
                            call_user_func([ $item, camel_case('apply_'.$attr.'_condition') ]);
                        }
                    }
                }
            }
        });

        static::saved(function($item) {
            /*
             *  Used for after post timeout condition a Job is fired and the Job id is saved
             *  causing function to be called again. Avoids re-running methods and getting
             *  stuck in an endless loop.
             */
            if(property_exists($item,'conditions_is_applied') && $item->conditions_is_applied) {
                // toggles back to resume functionality as before
                $item->conditions_is_applied = false;
                return;
            }
            if($item->applicableConditions) {
                // go through all registered condition attributes to run post application
                foreach($item->applicableConditions as $attr => $default) {

                    /* 
                    *   Only apply if:
                    *       1) The attribute is not the default value
                    *       2) The attribute has been changed (ie: dirty)
                    */
                    if($item->$attr !== $default) {
                        if(method_exists($item, camel_case('apply_'.$attr.'_post_condition'))) {
                            // method exists, lets apply the condition
                            call_user_func([ $item, camel_case('apply_'.$attr.'_post_condition') ]);
                        }
                    }
                }
            }
        });
    }
}