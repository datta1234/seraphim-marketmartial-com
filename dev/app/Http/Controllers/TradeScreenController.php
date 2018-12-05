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
        $total_rebate = 0;
        if($organisation) {
    	   $total_rebate = $organisation->rebates()->noTrade()->sum('amount');
        }
        
        $data = [
            'user' => $user, 
            'organisation' => $organisation, 
            'total_rebate' => $total_rebate
        ];
        if($user->isAdmin()) {
            $data['is_admin'] = true;
        }
        return view('pages.trade')->with($data);
    }
}
