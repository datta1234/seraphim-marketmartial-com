<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade\Rebate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\StructureItems\Market;

class RebatesSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $date_grouped_rebates = Rebate::where('organisation_id', $user->organisation->id)
            ->where('is_paid', true)
            ->whereYear('trade_date', Carbon::now()->year)
            ->get()
            // group date
            ->groupBy(function ($item, $key) {
                return Carbon::parse($item['trade_date'])->format('My');
            });

        foreach ($date_grouped_rebates as $date => $rebate) {
            // group market
            $date_grouped_rebates[$date] = $rebate->groupBy(function ($item, $key) {
                return $item->bookedTrade->tradeConfirmation->market->title;
            });
        }
        
        foreach ($date_grouped_rebates as $date => $market_rebates) {
            foreach ($market_rebates as $maket => $rebates) {
                foreach ($rebates as $key => $rebate) {
                    $rebate->pluck('');
                    $rebate['amount'] = $rebate->
                }
            }
        }

        $years = Rebate::where('organisation_id', $user->organisation->id)->select(
            DB::raw("YEAR(rebates.trade_date) as year")
        )->groupBy('year')->get();

        $markets = Market::all()->pluck('title')->toArray();
        // Logic to remove any occurrences of Delta One
        $index = null;
        foreach ($markets as $key => $market) {
            $index = strtoupper(str_replace(" ", "",$market)) == 'DELTAONE' ? $key : $index;
            if($index !== null) {
                unset($markets[$index]);
                $index = null;
            }
        }

        dd($markets,$rebates, $years->toArray());

        return view('rebates_summary.index')->with(compact('rebates', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if($request->has('year')) {
            $user = $request->user();
            $rebates = Rebate::where('organisation_id', $user->organisation->id)
                ->where('is_paid', true)
                ->whereYear('trade_date', $request->input('year'))
                ->get()
                ->transform(function($rebate) use ($user){
                    return $rebate->preFormat($user);
                })/*->groupBy(function ($item, $key) {
                    return Carbon::parse($item['trade_date'])->format('My');
                })*/;
        }
        dd($rebates, $user->organisation->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
