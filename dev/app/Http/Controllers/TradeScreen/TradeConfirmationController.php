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
        $this->authorize('phaseTwo', $tradeConfirmation);
        $tradeConfirmationUpdated = in_array($tradeConfirmation->trade_confirmation_status_id, [6,7]) ? $tradeConfirmation : $tradeConfirmation->createChild();
        $tradeConfirmationUpdated->setAccount($user,$request->input('trading_account_id')); 
        $exclude_list = array();
        /* Phase3 Update, no longer allowed to edit contracts */
        /*if(in_array($tradeConfirmationUpdated->marketRequest->trade_structure_slug, ['efp', 'rolls', 'efp_switch'])) {*/
            $exclude_list[] = 'Contract';
        /*}*/
    	$tradeConfirmationUpdated->updateGroups($request->input('trade_confirmation_data.structure_groups'), array(), $exclude_list);  
        try {
            $tradeConfirmationUpdated->phaseTwo();
        } catch(\App\Exceptions\SpotRefTooHighException $e) {
            return response()->json([
                'message' => 'Calculation error occured.', 
                'errors' => [ 
                    'trade_confirmation_data.structure_groups.'.$e->getCode() => [
                        ['spot' => $e->getMessage()]
                    ] 

                ]
            ], 422);
        }

        if($user->organisation_id == $tradeConfirmationUpdated->sendUser->organisation_id 
            && ($tradeConfirmationUpdated->trade_confirmation_status_id == 3))
        {
            $tradeConfirmationUpdated->trade_confirmation_status_id = 6;
        } 
        else if($user->organisation_id == $tradeConfirmationUpdated->recievingUser->organisation_id 
            && ($tradeConfirmationUpdated->trade_confirmation_status_id == 2 || $tradeConfirmationUpdated->trade_confirmation_status_id == 5)) 
        {
            $tradeConfirmationUpdated->trade_confirmation_status_id = 7;
        }


        $tradeConfirmationUpdated->save();

        $tradeConfirmationUpdated->notifyConfirmation($user->organisation,null);
        
        $data = $tradeConfirmationUpdated->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();


        return response()->json(['data' => $data]);
    }

    public function update(TradeConfirmation $tradeConfirmation,TradeConfirmationStoreRequest $request)
    {
        $user = $request->user();
        $this->authorize('update',$tradeConfirmation);
        $tradeConfirmationChild = $tradeConfirmation->createChild();
        $tradeConfirmationChild->setAccount($user,$request->input('trading_account_id'));
        /* Phase3 Update, no longer allowed to edit contracts */
        /*if(!in_array($tradeConfirmationChild->marketRequest->trade_structure_slug, ['efp', 'rolls', 'efp_switch'])) {
            $tradeConfirmationChild->updateGroups($request->input('trade_confirmation_data.structure_groups'), ['Contract']);
        }*/

        if($user->organisation_id == $tradeConfirmationChild->sendUser->organisation_id && $tradeConfirmationChild->trade_confirmation_status_id == 1)
        {
            $tradeConfirmationChild->trade_confirmation_status_id = 2;
            $tradeConfirmationChild->save();
            $tradeConfirmationChild->notifyConfirmation($tradeConfirmationChild->recievingUser->organisation,"Complete the booking in the confirmation tab");

        }
        else if($user->organisation_id == $tradeConfirmationChild->recievingUser->organisation_id && $tradeConfirmationChild->trade_confirmation_status_id == 2)
        {
            $tradeConfirmationChild->trade_confirmation_status_id = 3;
            $tradeConfirmationChild->save();
            $tradeConfirmationChild->notifyConfirmation($tradeConfirmationChild->sendUser->organisation,"Complete the booking in the confirmation tab");
        }

         $data = $tradeConfirmationChild->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();

        return response()->json(['message'=> 'Confirmation sent to counterparty','data' => $data]);
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
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->recievingUser->organisation,"Trade confirmation has been agreed.", 30000);
        }
        else if($user->organisation_id == $tradeConfirmation->recievingUser->organisation_id)
        {
            $tradeConfirmation->receiving_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmation->trade_confirmation_status_id = 4;
            $tradeConfirmation->save();
            $tradeConfirmation->notifyConfirmation($tradeConfirmation->sendUser->organisation,"Trade confirmation has been agreed.", 30000);
        }

        //perform the booked trades
        $tradeConfirmation->bookTheTrades();
        
        $data = $tradeConfirmation->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();

        // Send Notification email with the trade confirmation details to both parties
        $tradeConfirmation->notifyTradingPartyEmails();

        return response()->json(['data' => $data]);
    } 

    public function dispute(TradeConfirmation $tradeConfirmation,Request $request)
    {
        $user = $request->user();
        $this->authorize('dispute',$tradeConfirmation);
        $tradeConfirmationChild = $tradeConfirmation->createChild();
        /* Phase3 Update, no longer allowed to edit contracts */
        /*if(!in_array($tradeConfirmationChild->marketRequest->trade_structure_slug, ['efp', 'rolls', 'efp_switch'])) {
            $tradeConfirmationChild->updateGroups($request->input('trade_confirmation.structure_groups'), ['Contract']);
        }*/
        $tradeConfirmationChild->save();

        if($user->organisation_id == $tradeConfirmationChild->sendUser->organisation_id)
        {
            $tradeConfirmationChild->send_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmationChild->trade_confirmation_status_id = 5;
            $tradeConfirmationChild->save();
            $tradeConfirmationChild->notifyConfirmation($tradeConfirmationChild->recievingUser->organisation,"Your trade confirmation has been disputed, please review in the confirmation tab.", null);

        }
        else if($user->organisation_id == $tradeConfirmationChild->recievingUser->organisation_id)
        {
            $tradeConfirmationChild->receiving_trading_account_id = $request->input('trading_account_id');
            $tradeConfirmationChild->trade_confirmation_status_id = 3;
            $tradeConfirmationChild->save();
            $tradeConfirmationChild->notifyConfirmation($tradeConfirmationChild->sendUser->organisation,"Your trade confirmation has been disputed, please review in the confirmation tab.", null);
        }

        \Slack::postMessage([
            "text"      => $tradeConfirmationChild->getMessage('confirmation_disputed')
        ], 'dispute');

        $data = $tradeConfirmationChild->fresh()->load([
            'tradeConfirmationGroups'=>function($q)
            {
                $q->with(['tradeConfirmationItems','userMarketRequestGroup.userMarketRequestItems']);
            }
        ])->preFormatted();
         
        return response()->json(['data' => $data]);
    }    
}
