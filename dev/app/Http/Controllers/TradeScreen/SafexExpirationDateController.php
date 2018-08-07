<?php

namespace App\Http\Controllers\TradeScreen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StructureItems\MarketType;
use App\Models\StructureItems\SafexExpirationDate;
use Carbon\Carbon;

class  SafexExpirationDateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SafexExpirationDate::paginate(12);
    }
}
