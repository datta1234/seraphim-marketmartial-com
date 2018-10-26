<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade\Rebate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\StructureItems\Market;
use App\Models\UserManagement\User;
use App\Http\Requests\Rebates\SummaryYearRequest;

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
        $total_rebates = 0;
        foreach ($date_grouped_rebates as $date => $rebate) {
            // Calculate the total organisation rebate amount for the year
            $total_rebates += $rebate->sum(function ($single) {
                return $single->bookedTrade->amount;
            });

            // group market
            $date_grouped_rebates[$date] = $rebate->groupBy(function ($item, $key) {
                return $item->bookedTrade->market->title;
            });
        }

        foreach ($date_grouped_rebates as $date => $market_rebates) {
            foreach ($market_rebates as $maket => $rebates) {
                $market_rebates[$maket] = $rebates->groupBy(function ($item, $key) {
                    return $item->user->full_name;
                });

                foreach ($market_rebates[$maket] as $key => $rebate) {
                    //dd($rebates);
                    $market_rebates[$maket][$key] = $rebates->sum(function ($single) {
                        return $single->bookedTrade->amount;
                    });
                }
            }
        }

        $years = Rebate::where('organisation_id', $user->organisation->id)->select(
            DB::raw("YEAR(rebates.trade_date) as year")
        )->groupBy('year')->get();
        
        $users = User::where('organisation_id', $user->organisation->id)->pluck('full_name');

        return view('rebates_summary.index')
            ->with(compact('date_grouped_rebates', 'years', 'users', 'total_rebates'));
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
    public function show(SummaryYearRequest $request)
    {
        $user = $request->user();
        $rebates_query = Rebate::where('is_paid', true)->whereYear('trade_date', $request->input('year'));

        if($user->role_id != 1) {
            $rebates_query = $rebates_query->where('organisation_id', $user->organisation->id);
        }

        $rebates = $rebates_query->orderBy("trade_date", "ASC")->paginate(10);


        $rebates->transform(function($rebate) {
            return $rebate->preFormat();
        });
        
        return response()->json($rebates);
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
