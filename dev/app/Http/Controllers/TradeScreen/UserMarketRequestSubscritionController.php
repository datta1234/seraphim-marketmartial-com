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
        $user = \Auth::user();
        $message = '';
        // Checks subcribtion request - subscribe / unsubscribe
        if($request->input('market_request_subscribe')) {
            $user->userMarketRequestSubscriptions()->attach($userMarketRequest->id);
            $message = 'You will be alerted once this market clears';
        } else {
            $user->userMarketRequestSubscriptions()->detach($userMarketRequest->id);
            $message = 'You will no longer be alerted once this market clears';
        }

        $userMarketRequest->notifyRequested([$user->organisation]);
        return response()->json(['data' => null, 'message' => $message]);
    }
}
