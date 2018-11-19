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
            return response()->json(['data'=>null, 'message'=>'You will be alerted once this market clears']);
        } else {
            \Auth::user()->userMarketRequestSubscriptions()->detach($userMarketRequest->id);
            return response()->json(['data'=>null, 'message'=>'You will no longer be alerted once this market clears']);
        }

        return response()->json(["errors"=> null, 'message'=>'Failed to update alert status'], 500);
    }
}
