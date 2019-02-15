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
                FROM `trade_confirmations` parent_tc
                WHERE EXISTS (
                    SELECT * 
                    FROM users 
                    WHERE users.organisation_id = ?
                    AND (
                        id = parent_tc.send_user_id
                        OR id = parent_tc.receiving_user_id
                    )
                )
                AND NOT EXISTS (
                    SELECT *
                    FROM `trade_confirmations` sub_tc
                    WHERE sub_tc.trade_confirmation_id = parent_tc.id
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
