<?php

namespace App\Traits;

/** 
    Add the following to the model implementing activity

        protected function activityKey() {
            return $this->uniqueIdValueForActivity;
        }
*/
trait HasDismissibleActivity {
    
    /**
     * The trackActivity method of the model.
     *
     * @return void
     */
    public function trackActivity($key, $activity, $expiry = 5)
    {
        $activity_list = \Cache::get($this->activityKey(), []);
        $this->expireActivity(); // expire before trying to set

        // cant re-set whats there
        if(isset($activity_list[$key])) {
            return false;
        }
        
        $activity_list[$key] = [
            "activity" => $activity,
            "expiry" => $expiry == false ? false : now()->addMinutes($expiry)
        ];

        \Cache::put($this->activityKey(), $activity_list, now()->endOfDay());

        return true;
    }

    /**
     * The hasActivity method of the model.
     *
     * @return void
     */
    public function hasActivity($key, $return_expired = false)
    {
        $activity = \Cache::get($this->activityKey(), []);

        // cant re-set whats there
        if(isset($activity[$key])) {
            if($activity[$key]["expiry"] !== false && $activity[$key]["expiry"] < now()) {
                $this->expireActivity();
                return $return_expired ? true : false;
            }
        }
        return false;
    }

    /**
     * The getActivity method of the model.
     *
     * @return void
     */
    public function getActivity($key = null, $group = false, $return_expired = false)
    {
        $activity = \Cache::get($this->activityKey(), []);
        if($key == null) {
            return $activity;
        }

        // handle grouped activity
        if($group) {
            $out = [];
            // filter to only the ones matching the depth
            $activity = collect($activity)->filter(function($v, $k) use ($key) {
                return substr($k, 0, strlen($key)) == $key;
            });
            foreach($activity as $k => $val) {
                $parts = explode('.', $k);
                $current = &$out; // reset reference
                foreach($parts as $idx => $part) {
                    if($idx == count($parts)-1) {
                        // set the value
                        $current[$part] = $val['activity'];
                    } else {
                        // set the pointer to next level
                        $current[$part] = isset($current[$part]) ? $current[$part] : [];
                        $current = &$current[$part];
                    }
                }
            }
            $key_parts = explode('.', $key);
            foreach($key_parts as $part) {
                $out = $out != null && isset($out[$part]) ? $out[$part] : null;
            }
            return $out;
        }


        // cant re-set whats there
        if(isset($activity[$key])) {
            if($activity[$key]["expiry"] !== false && $activity[$key]["expiry"] < now()) {
                $this->expireActivity();
                return $return_expired ? $activity[$key]["activity"] : null;
            }
            return $activity[$key]["activity"];
        }
        return null;
    }

    /**
     * The dismissActivity method of the model.
     *
     * @return void
     */
    public function dismissActivity($key)
    {
        $activity = \Cache::get($this->activityKey(), []);
        $this->expireActivity(); // expire before trying to set
        $ret = null;

        if(isset($activity[$key])) {
            $ret = $activity[$key]["activity"];
            unset($activity[$key]);
        }
        \Cache::put($this->activityKey(), $activity, now()->endOfDay());

        // default null;
        return $ret;
    }

    /**
     * The expireActivity method of the model.
     *
     * @return void
     */
    public function expireActivity()
    {
        $activity = \Cache::get($this->activityKey(), []);
        $changed = false;

        foreach($activity as $key => $value) {
            if($activity[$key]["expiry"] !== false && $activity[$key]["expiry"] < now()) {
                unset($activity[$key]); // expire activity
                $changed = true;
            }
        }

        \Cache::put($this->activityKey(), $activity, now()->endOfDay());
        return $changed;
    }

}