<?php
namespace App\Facades\Logging;

use Illuminate\Support\Facades\Facade;

class ActivityLogger extends Facade
{
    protected static function getFacadeAccessor() {
        return 'ActivityLogger';
    }
}