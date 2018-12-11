<?php

namespace App\Http\Controllers\TradeScreen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\StructureItems\MarketType;
use App\Http\Requests\TradeScreen\TradeConfirmationStoreRequest;

class TradeConfirmationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function phaseTwo(TradeConfirmation $tradeConfirmation,TradeConfirmationStoreRequest $request)
    {
        $user = $request->user();
        $this->authorize('phaseTwo',$tradeConfirmation);
        $tradeConfirmation->setAccount($user,$request->input('trading_account_id'));
    	$tradeConfirmation->updateGroups($request->input('trade_confirmation_data.structure_groups'));    	
        $tradeConfirmation->phaseTwo();  
        $tradeConfirmation->save();
        
        $data = $tradeConfirmation->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();


        return response()->json(['data' => $data]);
    }

    public function update(TradeConfirmation $tradeConfirmation,Request $request)
    {
        $user = $request->user();
        $this->authorize('update',$tradeConfirmation);
        $tradeConfirmation->setAccount($user,$request->input('trading_account_id'));
     
        if($user->organisation_id == $tradeConfirmation->sendUser->organisation_id && $tradeConfirmation->trade_confirmation_status_id == 1)
        {
            $tradeConfirmation->trade_confirmation_status_id = 2;
            $tradeConfirmation->save();
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->recievingUser->organisation,"Congrats on the trade! Complete the booking in the confirmation tab");

        }
        else if($user->organisation_id == $tradeConfirmation->recievingUser->organisation_id && $tradeConfirmation->trade_confirmation_status_id == 2)
        {
            $tradeConfirmation->trade_confirmation_status_id = 3;
            $tradeConfirmation->save();
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->sendUser->organisation,"Congrats on the trade! Complete the booking in the confirmation tab");
        }

         $data = $tradeConfirmation->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();

        return response()->json(['trade_confirmation' => $data]);
    }

    public function confirm(TradeConfirmation $tradeConfirmation,Request $request)
    {
        $user = $request->user();
        $this->authorize('confirm',$tradeConfirmation); 
        $tradeConfirmation->setAccount($user,$request->input('trading_account_id'));
        $tradeConfirmation->save();
        if($user->organisation_id == $tradeConfirmation->sendUser->organisation_id)
        {
            $tradeConfirmation->send_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmation->trade_confirmation_status_id = 4;
            $tradeConfirmation->save();
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->recievingUser->organisation,"Trade Has been succefully been booked.");

        }
        else if($user->organisation_id == $tradeConfirmation->recievingUser->organisation_id)
        {
            $tradeConfirmation->receiving_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmation->trade_confirmation_status_id = 4;
            $tradeConfirmation->save();
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->sendUser->organisation,"Trade Has been succefully been booked.");
        }

        /*
        *   
        */
        $tradeConfirmation->bookTheTrades();
        
        $data = $tradeConfirmation->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();


         //perform the booked trades

        return response()->json(['data' => $data]);
    } 

    public function dispute(TradeConfirmation $tradeConfirmation,Request $request)
    {
        $user = $request->user();
        $this->authorize('dispute',$tradeConfirmation);  
        if($user->organisation_id == $tradeConfirmation->sendUser->organisation_id)
        {
            $tradeConfirmation->send_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmation->trade_confirmation_status_id = 5;
            $tradeConfirmation->save();
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->recievingUser->organisation,"Your Trade has been disputed please review the contents through confirmation tab");

        }
        else if($user->organisation_id == $tradeConfirmation->recievingUser->organisation_id)
        {
            $tradeConfirmation->receiving_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmation->trade_confirmation_status_id = 3;
            $tradeConfirmation->save();
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->sendUser->organisation,"Your Trade has been disputed please review the contents through confirmation tab");
        }

        \Slack::postMessage([
            "text"      => $tradeConfirmation->getMessage('confirmation_disputed')
        ], 'dispute');

        $data = $tradeConfirmation->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();
         
        return response()->json(['data' => $data]);
    }    
}
