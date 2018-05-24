<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\User;

class TradeScreenController extends Controller
{
    public function index()
    {
        return view('pages.trade');
    }
}
