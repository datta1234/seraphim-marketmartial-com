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
        $user = $request->user();
    	return TradeConfirmation::where(function($q) use($user,$marketType)
        {
            $q->sentByMyOrganisation($user->organisation_id)
                ->marketType($marketType->id)
                ->whereIn('trade_confirmation_status_id',[1,5]);

        })->orWHere(function($q) use($user,$marketType){
            
            $q->sentToMyOrganisation($user->organisation_id)
                ->marketType($marketType->id)
                ->whereIn('trade_confirmation_status_id',[2,3]);

        })->get()
        ->map(function($item){
            return $item->preFormatted();
        });
    }
}
