<?php

namespace App\Http\Controllers\Stats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StructureItems\SafexExpirationDate;

class SafexExpirationDateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(SafexExpirationDate::all()->pluck("expiration_date","id"));
    }
}
