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

        $server_time = now()->toIso8601String();

        $closing_time = Carbon::createFromTimeString(config('marketmartial.window.trade_view_end_time'))
            ->toIso8601String();
        
        return view('pages.trade')->with([
            'user'          => $user, 
            'organisation'  => $organisation, 
            'total_rebate'  => $total_rebate,
            'server_time'   => $server_time,
            'closing_time'  => $closing_time
        ]);
    }
}
