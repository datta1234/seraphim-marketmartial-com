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
        $this->authorize('listTradeConfirmations', TradeConfirmation::class);
    	$trade_confirmations = TradeConfirmation::where(function($q) use($user,$marketType)
        {
            $q->sentByMyOrganisation($user->organisation_id)
                ->marketType($marketType->id)
                ->whereIn('trade_confirmation_status_id',[1,3,6]);

        })->orWhere(function($q) use($user,$marketType){
            
            $q->sentToMyOrganisation($user->organisation_id)
                ->marketType($marketType->id)
                ->whereIn('trade_confirmation_status_id',[2,5,7]);

        })->get()
        ->map(function($item){
            return $item->preFormatted();
        });


        return response()->json([
            'data'=> $trade_confirmations,
            'message'=>"Trade Confirmations loaded successfully.", 
        ]);
    }
}
