<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StructureItems\Market;
use App\Http\Requests\Admin\MarketsUpdateRequest;
use DB;

class MarketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$markets = Market::all();
    	return view('admin.markets.index')->with(compact('markets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MarketsUpdateRequest $request)
    {
    	$spot_prices = $request->input("spot_price_ref");
    	$market_errors = [];
    	try {
            DB::beginTransaction();
        	foreach ($spot_prices as $key => $spot_price) {
        		$market = Market::find($key);
        		if($market) {
	    			$market_update_result = $market->update(['spot_price_ref' => $spot_price]);
        		} else {
        			$market_errors[] = "Failed to locate market";
        		}
	    	}    
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to update the Market spot price references.');
        }

        if(!empty($market_errors)) {
        	return redirect()->back()->with('market_errors', $market_errors);
        }

        return redirect()->back()
        	->with('success', 'Market spot price references has been successfully updated.');
    }
}
