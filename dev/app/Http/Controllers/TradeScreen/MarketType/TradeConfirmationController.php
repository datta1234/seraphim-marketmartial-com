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
        $trade_confirmations = TradeConfirmation::where(function($tlq) use($user,$marketType)
        {
            $tlq->where(function($q) use($user,$marketType)
            {
                $q->sentByMyOrganisation($user->organisation_id)
                    ->marketType($marketType->id)
                    ->whereIn('trade_confirmation_status_id',[1,3,6]);

            })->orWhere(function($q) use($user,$marketType){
                
                $q->sentToMyOrganisation($user->organisation_id)
                    ->marketType($marketType->id)
                    ->whereIn('trade_confirmation_status_id',[2,5,7]);

            });
        })->whereRaw("
            id in (
                SELECT max(id) as 'id'
                FROM `trade_confirmations` 
                WHERE exists (
                    SELECT * 
                    FROM users 
                    WHERE users.organisation_id = ?
                    AND (
                        id = `trade_confirmations`.send_user_id
                        OR id = `trade_confirmations`.send_user_id
                    )
                )
                GROUP BY `trade_negotiation_id`, `trade_confirmation_status_id`
            )
        ", [$user->organisation_id])
        ->get()
        ->map(function($item){
            return $item->preFormatted();
        });


        return response()->json([
            'data'=> $trade_confirmations,
            'message'=>"Trade Confirmations loaded successfully.", 
        ]);
    }
}
