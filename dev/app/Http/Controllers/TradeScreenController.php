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

        $sa_local_time = now()->toIso8601String();
        
        return view('pages.trade')->with([
            'user'          => $user, 
            'organisation'  => $organisation, 
            'total_rebate'  => $total_rebate,
            'sa_local_time' => $sa_local_time
        ]);
    }
}
