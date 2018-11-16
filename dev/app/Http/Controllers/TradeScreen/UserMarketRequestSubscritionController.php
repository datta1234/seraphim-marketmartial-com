<?php

namespace App\Http\Controllers\TradeScreen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MarketRequest\UserMarketRequest;
use App\Http\Requests\TradeScreen\ToggleAlertClearedRequest;

class UserMarketRequestSubscritionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ToggleAlertCleared(UserMarketRequest $userMarketRequest,ToggleAlertClearedRequest $request)
    {
        if($request->input('market_request_subscribe')) {
        	\Auth::user()->userMarketRequestSubscriptions()->attach($userMarketRequest->id);
            return response()->json(['data'=>null, 'message'=>'Successfully subscribed to Market Request']);
        } else {
            \Auth::user()->userMarketRequestSubscriptions()->detach($userMarketRequest->id);
            return response()->json(['data'=>null, 'message'=>'Successfully unsubscribed to Market Request']);
        }

        return response()->json(["errors"=> null, 'message'=>'Failed to update Market Request subscription'], 500);
    }
}
