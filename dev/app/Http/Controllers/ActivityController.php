<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Market\UserMarket;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\StructureItems\MarketType  $marketType
     * @return \Illuminate\Http\Response
     */
    public function userMarket(UserMarket $user_market, $activity)
    {
        $org_id = \Auth::user()->organisation_id;
        $done = $user_market->dismissActivity("organisation.".$org_id.".".$activity);
        return response()->json([
            'success'   => true,
            'message'   => "Activity Dismissed",
            'activity'  => $user_market->getActivity("organisation.".$org_id, true)
        ]);
    }
}
