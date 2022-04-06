<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\User;
use App\Models\Trade\Rebate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TradeScreenController extends Controller
{
    public function index()
    {
    	$user = Auth::user();
    	$organisation = $user->organisation;

        if($user->isAdmin()) {
           $total_rebate = Rebate::noTrade()->currentMonth()->sum('amount');
        } else {
    	   $total_rebate = $organisation->rebates()->noTrade()->currentMonth()->sum('amount');
        }

        $now = Carbon::now();
        $server_time = $now->toIso8601String();

        $closing_time = Carbon::createFromTimeString(config('marketmartial.window.trade_view_end_time'))
            ->toIso8601String();

        $trade_start = Carbon::createFromTimeString(config('marketmartial.window.trade_start_time_display_only'));
        // if we are already in trading time
        if( $now->gt($trade_start) ) {
            $trade_end = Carbon::createFromTimeString(config('marketmartial.window.trade_end_time_display_only'));
            // if we have passed the closing time
            if( $now->gt($trade_end) ) {
                // add 1 day to make it tomorrow
                $trade_start = $trade_start->addDays(1);
            }
        }
        
        return view('pages.trade')->with([
            'user'          => $user, 
            'organisation'  => $organisation, 
            'total_rebate'  => $total_rebate,
            'server_time'   => $server_time,
            'closing_time'  => $closing_time,
            'quick_messages'=> config('marketmartial.slack.quick_messages'),
            'trade_start'   => $trade_start->toIso8601String()
        ]);
    }
}
