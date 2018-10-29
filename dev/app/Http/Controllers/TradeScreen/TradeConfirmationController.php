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

    public function update(TradeConfirmation $tradeConfirmation,Request $request)
    {
        $user = $request->user();        
        if($user->organisation_id == $tradeConfirmation->sendUser->organisation_id && $tradeConfirmation->trade_confirmation_status_id == 1)
        {
            $tradeConfirmation->send_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmation->trade_confirmation_status_id = 2;
            $tradeConfirmation->save();
            $tradeConfirmation->sendToReciever("Congrats on the trade! Compelete the booking in the confirmation tab");

        }else if($user->organisation_id == $tradeConfirmation->recievingUser->organisation_id && $tradeConfirmation->trade_confirmation_status_id == 2)
        {
            $tradeConfirmation->receiving_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmation->trade_confirmation_status_id = 3;
            $tradeConfirmation->save();
            $tradeConfirmation->sendToInitiate("Congrats on the trade! Compelete the booking in the confirmation tab");

        }

    }    
}
