<?php

namespace App\Http\Controllers\TradeScreen\MarketType;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\StructureItems\MarketType;

class TradeConfirmationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,MarketType $marketType)
    {
        //where organisation is involved
        $user = $request->user();
    	return TradeConfirmation::where(function($q) use($user)
        {
            $q->sentByMyOrganisation($user->organisation_id)
            ->where('trade_confirmation_status_id',1);

        })->orWHere(function($q) use($user){
            $q->sentToMyOrganisation($user->organisation_id)
            ->where('trade_confirmation_status_id',2);
        })->get()
        ->map(function($item){
            return $item->preFormatted();
        });
    }
}
