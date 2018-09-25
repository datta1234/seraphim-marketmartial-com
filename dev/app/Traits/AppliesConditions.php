<?php

namespace App\Traits;

/** 
    Add the following to the model applying conditions, then add the applicable conditions
    
        protected $applicableConditions = [
            'attribute_name' => 'default_value'
        ];

    Conditons are applied based on a method name pattern of "apply<_ConditonNameInCamelCase_>Condtion"
    eg: applyMyThingCondition(){ ... }
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
            if($item->applicableConditions) {
                // go through all registered condition attributes
                foreach($item->applicableConditions as $attr => $default) {
                    \Log::info([$attr, $item->$attr, $default, $item->isDirty($attr)]);
                    if($item->$attr !== $default && $item->isDirty($attr)) {
                        if(method_exists($item, camel_case('apply_'.$attr.'_condition'))) {
                            // method exists, lets apply the condition
                            call_user_func([ $item, camel_case('apply_'.$attr.'_condition') ]);
                        }
                    }
                }
            }
        });
    }
}