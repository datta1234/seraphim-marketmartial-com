<?php

namespace App\Http\Controllers\TradeScreen;

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
    public function phaseTwo(TradeConfirmation $tradeConfirmation,Request $request)
    {
    	$tradeConfirmation->updateGroups($request->input('structure_groups'));    	
        $tradeConfirmation->phaseTwo();
        $data = $tradeConfirmation->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();
        
        return response()->json(['trade_confirmation' => $data]);
    }

    
}
