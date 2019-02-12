<?php

namespace App\Traits;

trait ScopesToPreviousDay {

    /**
    * Scope the query to the previous day
    * @param $query \Illuminate\Database\Eloquent\Builder
    *
    * @return $query \Illuminate\Database\Eloquent\Builder
    */
    public function scopePreviousDay($query)
    {
        $start = $this->resolvePreviousDayStart();
        $end = $this->resolvePreviousDayEnd();
        return $query->whereBetween('updated_at', [ $start, $end ]);
    }

    /**
    * return previous trading day start
    *
    * @return $start \Carbon\Carbon
    */
    public function resolvePreviousDayStart()
    {
        // if monday
        if(now()->dayOfWeekIso == 1) {
            return $start = now()->subDays(3)->startOfDay(); // friday 00:00:00
        } 
        // any other day
        return $start = now()->subDays(1)->startOfDay();
    }

    /**
    * return previous trading day end
    *
    * @return $end \Carbon\Carbon
    */
    public function resolvePreviousDayEnd()
    {
        // if monday
        if(now()->dayOfWeekIso == 1) {
            return $end = now()->subDays(2)->startOfDay(); // sat 00:00:00
        } 
        // any other day
        return $end = now()->startOfDay();
    }

}