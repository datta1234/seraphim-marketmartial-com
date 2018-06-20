<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\User;
use Illuminate\Support\Facades\Auth;

class TradeScreenController extends Controller
{
    public function index()
    {
    	$user = Auth::user();
    	$organisation = $user->organisation;
    	$total_rebate = $user->userTotalRebate();
        
        return view('pages.trade')->with([
        	'user' => $user, 
        	'organisation' => $organisation, 
        	'total_rebate' => $total_rebate
        ]);
    }
}
