<?php

namespace App\Helpers\Misc;
use Carbon\Carbon;

class TimeRestrictions
{
	public static function canLogin($date)
	{
		$startTime = Carbon::createFromTimeString(config('marketmartial.window.operation_start_time'));
        $endTime = Carbon::createFromTimeString(config('marketmartial.window.operation_end_time'));
        $daysOffline = explode(',',config('marketmartial.window.days_offline'));

        return $date->between($startTime,$endTime) && !in_array(Carbon::now()->format("D"), $daysOffline);
	}

	public static function canTrade($date)
	{
		$startTime = Carbon::createFromTimeString(config('marketmartial.window.trade_start_time'));
        $endTime = Carbon::createFromTimeString(config('marketmartial.window.trade_end_time'));
        return $date->between($startTime,$endTime);
	}

	public static function canViewTrade($date)
	{
		$startTime = Carbon::createFromTimeString(config('marketmartial.window.trade_view_start_time'));
        $endTime = Carbon::createFromTimeString(config('marketmartial.window.trade_view_end_time'));
        return $date->between($startTime,$endTime);
	}
}