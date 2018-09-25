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
        
    /**
     * @todo 
     *consider using the existing controller and passing a param not paginate
     */
        return response()->json(SafexExpirationDate::all()->pluck("expiration_date","id"));
    }
}
