<?php

namespace App\Traits;
use App\Helpers\Misc\ResolveUuid;
// use App\Events\ModelSaved;
use Illuminate\Support\Facades\Cache;


trait ModelCache {

    public static function getCached($minutes = 60)
    {
        return Cache::remember((new self)->getTable(), $minutes, function () {
            return self::all();
        });
    }

    public static function purgeCached()
    {
        return Cache::forget((new self)->getTable());
    }
}