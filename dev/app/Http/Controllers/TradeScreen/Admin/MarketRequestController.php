<?php

namespace App\Http\Controllers\TradeScreen\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MarketRequest\UserMarketRequest;

class MarketRequestController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marketRequest = UserMarketRequest::findOrFail($id);
        return response()->json([
            'quotes' => $marketRequest->userMarkets->map(function($item) {
                return $item->preFormattedQuote(true);
            })
        ]);
    }
}
